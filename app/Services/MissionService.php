<?php
namespace App\Services;


use App\Entities\MissionResolution;
use Exception;
class MissionService
{
    protected $missionModel;
    protected $heroModel;
    protected $missionResolutionModel;
    protected $missionHeroModel;
    protected $playerModel;

    public function __construct() {
        $this->missionModel = model('MissionModel');
        $this->heroModel = model('HeroModel');
        $this->playerModel = model('PlayerModel');
        $this->missionResolutionModel = model('MissionResolutionModel');
        $this->missionHeroModel = model('MissionHeroModel');
    }

    /**
     * Démarrer une mission.
     * Vérification de l'existence de la mission.
     * Vérification des Héros(existant,appartenant aux joueurs,disponibilité).
     * Vérification de la possibilité de les envoyer en mission.
     * @param Player $player Entité joueur
     * @param int $missionId Identifiant de la mission
     * @param array $heroesIds Liste des héros (ID)
     * @return int Identifiant de la Mission Resolution
     * @throws Exception
     */
    public function startMission($player, int $missionId,array $heroesIds)
    {
        $mission = $this->missionModel->find($missionId);
        if(!$mission) {
            throw new Exception('Mission introuvable');
        }
        if(empty($heroesIds)) {
            throw new Exception('Aucun héros selectionné');
        }

        $heroes = $this->heroModel
            ->where('player_id', $player->id)
            ->whereIn('id', $heroesIds)
            ->findAll();

        if(count($heroes) !== count($heroesIds)) {
            throw new Exception("Un ou plusieurs mercenaires ne sont pas à vous");
        }

        $busyHeroIds = $this->missionHeroModel->getBusyHeroIds($player->id);
        foreach($heroes as $hero){
            if(in_array((int)$hero->id, $busyHeroIds,true)) {
                throw new Exception("Un héro proposé est déjà en mission.");
            }
        }
        $this->validateSquad($mission, $heroes);

        return $this->dispatchSquad($player, $heroes, $mission);
    }

    /**
     * Pour chaque mission du joueur terminé, on lance la méthode de résolution qui inclut la distribution des récompenses
     * @param Player $player Entité joueur
     * @return array
     * @throws \ReflectionException
     */
    public function resolveFinishedMission($player){
        $resolved = [];
        foreach($this->missionResolutionModel->getMissionFinishedNotResolved($player->id)as $mr){
            $result = $this->resolveMission($player, $mr);
            if($result !== null){
                $resolved[] = $result;
            }
        }
        return $resolved;
    }

    /**
     * Création de la mission Resolution puis stockage des héros envoyés dans la mission
     * @param Player $player Entité joueur
     * @param array $heroes Liste des héros (entité)
     * @param $mission Entité mission
     * @return int Identifiant de la Mission Resolution
     * @throws \ReflectionException
     */
    private function dispatchSquad($player,array $heroes,$mission){
        $staminaRequired = (int) $mission->getStaminaRequired();
                $endsAt = date('Y-m-d H:i:s', time() + (int) $mission->duration);

        $mrId = $this->missionResolutionModel->insert([
            'player_id' => $player->id,
            'mission_id' => $mission->id,
            'success' => false,
            'ends_at' => $endsAt,
        ]);

        $missionHeroes =[];
        foreach($heroes as $hero){
            $missionHeroes[] = ['missions_resolution_id' => $mrId,'hero_id' => $hero->id];

            $newStamina = max(0,(int) $hero->stamina_current - $staminaRequired);
            $this->heroModel->update($hero->id, [
                'stamina_current' => $newStamina,
                'last_stamina_update'=>$endsAt,
            ]);
        }
        $this->missionHeroModel->insertBatch($missionHeroes);
        return (int) $mrId;
    }

    /**
     * Vérification de l'équipe. Les pré-requis de la mission sont OK.
     * Puissance demandée suffisante.
     * Héros avec assez de stamina et spécialisation OK.
     * @param Mission $mission Entité Mission
     * @param array $heroes Liste des Héros (Entité)
     * @return void
     * @throws Exception
     */
    private function validateSquad($mission, array $heroes)
    {
        $totalPower = 0;
        $squadSpecIds = array();
        $staminaRequired=(int) $mission->getStaminaRequired();
        foreach($heroes as $hero) {
            if((int) $hero->stamina_current < $staminaRequired){
                throw new Exception("Le héros {$hero->name} n'a pas assez d'endurances");
            }
//            Addition de la puissance à chaque boucle du foreach
            $totalPower += (int) $hero->power;

            $spec = $hero->getHeroModel()->getSpecialization();
            if($spec){
                $squadSpecIds[] = (int) $spec['id'];
            }
        }
        if($totalPower < (int) $mission->getPowerRequired()){
            throw new Exception("La puissance de votre escouade est bien trop faible");
        }

        $requiredSpecs = $mission->getSpecializations();
        if(!empty($requiredSpecs)){
            foreach($requiredSpecs as $spec){
                $specId = (int) $spec['id'];
                if(!in_array($specId, $squadSpecIds)){
                    throw new Exception("L'escouade ne possède pas les spécialisations requises.");
                }
            }
            }
    }

    /**
     * Calcul des récompenses aléatoirement
     * @param Mission $mission Entité Mission
     * @return int[] Tableau des récompenses (entier)
     */
    private function calculateRewards($mission)
    {
        $creditGain = mt_rand((int)$mission->credits_reward_min,(int)$mission->credits_reward_max);
        $energyGain = mt_rand((int)$mission->energy_reward_min,(int)$mission->energy_reward_max);
        $xpGain = mt_rand((int)$mission->experience_reward_min,(int)$mission->experience_reward_max);

        return[
            'credits'=>$creditGain,
            'energy'=>$energyGain,
            'xp'=>$xpGain,
        ];
    }

    /**
     * Termine une mission (Mission Resolution) et attribue les récompenses au joueur.
     * @param $player
     * @param $mr
     * @return array
     * @throws \ReflectionException
     */
    private function resolveMission($player, $mr)
    {
        $mission = $mr->getMission();
        $rewards = $this->calculateRewards($mission);

        $now = date('Y-m-d H:i:s');
        $dataUpdate = [
            'success'=> 1,
            'credits_gained'=> $rewards['credits'],
            'energy_gained'=> $rewards['energy'],
            'experience_gained'=> $rewards['xp'],
            'resolved_at'=> $now,
        ];

        $mr->fill($dataUpdate);

        $this->missionResolutionModel->save($mr);

        $player->credits += $rewards['credits'];
        $player->fusion_energy += $rewards['energy'];
        $player->experience += $rewards['xp'];
        $this->playerModel->save($player);

        return ['mission'=>$mission] + $rewards;

    }
}