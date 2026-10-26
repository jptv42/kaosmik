<?php

namespace Tests\Database\Services;

use App\Entities\Hero;
use App\Models\CantinaModel;
use App\Models\PlayerModel;
use App\Services\CantinaService;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class CantinaServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    private CantinaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CantinaService();
    }

    //Le temps restant avant renouvellement
    //Les offres durent 12 heures.
    public function testRemainingSecondsOfRecentOffers()
    {
        //->subHours(11) : Soustrait 11 heures à cet instant présent.
        $createdAt = Time::now()->subHours(11);

        //getRemainingSeconds($createdAt) : On demande au service combien de temps il reste avant la fin (par exemple, si une mission dure 12 heures au total)
        $this->assertEqualsWithDelta(3600, $this->service->getRemainingSeconds($createdAt), 5);
    }

    //Test si le temps restant en seconde ne dépasse pas 0 si le temps écoulé dépasse 12 heures(ici 13h)
    public function testRemainingSecondsIsZeroWhenOffersExpired()
    {
        $createdAt = Time::now()->subHours(13);

        $this->assertSame(0, $this->service->getRemainingSeconds($createdAt));
        $this->assertSame('00:00:00', $this->service->getRemainingTime($createdAt));
    }

    //La génération des offres
    public function testGenerateOffersCreatesThreeOffersForTheLevelOfThePlayer(){
        $player = $this->createPlayer('joueur1');
        $offers = $this->service->generateOffers($player->id);

        $this->assertCount(3, $offers);
        //Niveau 1: seul le modèle "Cadet de la Flotte" (id 1) est disponible
        foreach ($offers as $offer) {
            $this->assertSame(1, $offer->hero_model_id);
        }
    }
    public function testGenerateOffersReplacesPreviousOffers(){
        $player = $this->createPlayer('joueur1');

        $this->service->generateOffers($player->id);
        $this->service->generateOffers($player->id);

        $this->seeNumRecords(3, 'cantinas',['player_id'=>$player->id]);
    }

    //Le recrutement
    //Test le recrutement réussi, crée le héros et débite le joueur
    public function testRecruitCreatesHeroAndChargesPlayer(){
        $player = $this->createPlayer('joueur1');
        $offer = $this->service->generateOffers($player->id)[0];

        $hero = $this->service->recruit($offer->id);

        $this->assertInstanceOf(Hero::class, $hero);
        $this->assertSame($player->id,$hero->player_id);
        $this->assertSame(1000 - $offer->cost_credit, (new PlayerModel())->find($player->id)->credits);
    }

    public function testRecruitFailsWhenPlayerCannotAfford(){
        // 1. Préparation : création d'un joueur (qui n'a pas assez de crédits) et récupération d'une offre
        $player = $this->createPlayer('joueur1',['credits'=>0]);
        $offer = $this->service->generateOffers($player->id)[0];

        // 2. Action & Assertion : tenter de recruter sans assez d'argent doit échouer et retourner null
        $this->assertNull($this->service->recruit($offer->id));

        // 3. Vérification en base de données : aucun héros ne doit avoir été créé pour ce joueur
        $this->seeNumRecords(0, 'heroes', ['player_id' => $player->id]);
    }

    public function testRecruitFailsWhenFleetIsFull(){
        $player = $this->createPlayer('joueur1',['fleet_capacity'=>1]);
        $this->createHero($player);
        $offer = $this->service->generateOffers($player->id)[0];

        $this->assertNull($this->service->recruit($offer->id));
    }
    public function testRecruitUnknownOfferReturnsNull(){
        $this->assertNull($this->service->recruit(99999));
    }
}
