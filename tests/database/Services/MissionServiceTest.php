<?php
//MissionService::processMission($player, $missionId, $heroesIds)
// vérifie l'équipe (propriétaire, endurance, puissance),
// tire les récompenses, enregistre la mission et applique les effets.
// Quand une règle n'est pas respectée, elle lève une Exception avec un message en français.

namespace Tests\Database\Services;

use App\Models\HeroModel;
use App\Models\PlayerModel;
use App\Services\MissionService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class MissionServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    private MissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MissionService();
    }

    //Une mission inconnue
    public function testUnknownMissionThrowsException(){
        $player = $this->createPlayer('joueur1');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mission introuvable');

        $this->service->processMission($player,9999,[1]);
    }


    //Une équipe vide
    public function testEmptySquadThrowsException(){
        $player = $this->createPlayer('joueur1');
        $this->expectExceptionMessage('Aucun héros selectionné');
        $this->service->processMission($player,1,[]);
    }

    //Le mercenaire d'un autre joueur
    public function testCannotUseHeroOfAnotherPlayer(){
        $player = $this->createPlayer('joueur1');
        $other = $this->createPlayer('joueur2');
        $hero = $this->createHero($other);

        $this->expectExceptionMessage('Un ou plusieurs mercenaires ne sont pas à vous');

        $this->service->processMission($player,1,[$hero->id]);
    }

    //Une équipe trop faible
    public function testSquadTooWeakThrowsException()
    {
        $player = $this->createPlayer('joueur1');
        $hero = $this->createHero($player, ['power' => 10]);

        //Mission 1 : puissance requide 30
        $this->expectExceptionMessage('La puissance de votre escouade est bien trop faible');

        $this->service->processMission($player, 1, [$hero->id]);
    }

    //Un mercenaire épuisé
    public function testTiredHeroThrowsException()
    {
        $player = $this->createPlayer('joueur1');
        $hero   = $this->createHero($player, [
            'name'            => 'Fatigue Test',
            'power'           => 40,
            'stamina_current' => 5,
        ]);

        //Mission 1 : endurance requise 20
        $this->expectExceptionMessage("Le héros Fatigue Test n'a pas assez d'endurances");

        $this->service->processMission($player,1,[$hero->id]);

    }
    //Une mission réussie
    //Les récompenses sont tirées au hasard dans une fourchette :
    // le test vérifie donc des bornes et des cohérences
    // (les crédits gagnés sont bien ajoutés au joueur) plutôt qu'une valeur exacte.
    public function testSuccessfulMissionGivesRewardsAndConsumesStamina()
    {
        $player = $this->createPlayer('joueur1');
        $hero   = $this->createHero($player, ['power' => 40, 'stamina_current' => 100]);

        $rewards = $this->service->processMission($player, 1, [$hero->id]);

        // Mission 1 : 30-60 crédits, 15-25 xp, 20 d'endurance
        $this->assertGreaterThanOrEqual(30, $rewards['credits']);
        $this->assertLessThanOrEqual(60, $rewards['credits']);

        $this->seeInDatabase('mission_resolutions', ['player_id' => $player->id, 'mission_id' => 1]);
        $this->seeInDatabase('heroes', ['id' => $hero->id, 'stamina_current' => 80]);

        $fresh = (new PlayerModel())->find($player->id);
        $this->assertSame(1000 + $rewards['credits'], $fresh->credits);
        $this->assertSame($rewards['xp'], $fresh->experience);
    }


}
