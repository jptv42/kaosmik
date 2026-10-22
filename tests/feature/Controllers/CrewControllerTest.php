<?php

namespace Tests\Feature\Controllers;

use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class CrewControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use AuthenticationTesting;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetServices();
        $this->mockSession();
    }
    //Afficher l'équipage
    public function testCrewPageListsHeroes()
    {
        $player = $this->createPlayer('joueur1');
        $this->createHero($player, ['name' => 'Capitaine Test']);
        $this->actingAs($this->getUserOf($player));

        $result = $this->get('/equipage');

        $result->assertStatus(200);
        $result->assertSee('Capitaine Test');
    }
    //Licencier un mercenaire
    public function testSellHeroGivesBackHalfOfItsCost()
    {
        $player = $this->createPlayer('joueur1');
        $hero   = $this->createHero($player, ['cost_credit' => 100]);
        $this->actingAs($this->getUserOf($player));

        $result = $this->post('/equipage/sell/' . $hero->id);

        $result->assertRedirectTo(site_url('/equipage'));
        $this->dontSeeInDatabase('heroes', ['id' => $hero->id]);
        // 1050 crédits de départ
        $this->seeInDatabase('players', ['id' => $player->id, 'credits' => 1050]);
    }

    //Licencier le mercenaire d'un autre joueur
    //Exercice : ce test est juste et doit échouer (« Failure »). Lisez attentivement le message :
    // PHPUnit vous indique l'adresse attendue et l'adresse réellement obtenue.
    // Corrigez la méthode du contrôleur concernée et relancez le test.
    // Ne modifiez pas le test.
    public function testSellHeroOfAnotherPlayerIsRefused()
    {
        $owner    = $this->createPlayer('proprietaire');
        $intruder = $this->createPlayer('intrus');
        $hero     = $this->createHero($owner);
        $this->actingAs($this->getUserOf($intruder));

        $result = $this->post('/equipage/sell/' . $hero->id);

        // Le héros est toujours là et on est renvoyé vers la page de l'équipage
        $this->seeInDatabase('heroes', ['id' => $hero->id]);
        $result->assertRedirectTo(site_url('/crew'));
    }

    public function testSellBulkSellsOnlyMyHeroes()
    {
        //Pour le calcul il faut prendre en compte le fait qu'à la revente le prix est diminué de 50%
        $me    = $this->createPlayer('joueur1');
        $other = $this->createPlayer('joueur2');
        $mine1 = $this->createHero($me, ['cost_credit' => 100]);
        $mine2 = $this->createHero($me, ['cost_credit' => 60]);
        $theirs = $this->createHero($other);
        $this->actingAs($this->getUserOf($me));

        $result = $this->post('/equipage/sell-bulk', ['ids' => [$mine1->id, $mine2->id, $theirs->id]]);

        $result->assertRedirectTo(site_url('/equipage'));
        $this->dontSeeInDatabase('heroes', ['id' => $mine1->id]);
        $this->dontSeeInDatabase('heroes', ['id' => $mine2->id]);
        $this->seeInDatabase('heroes', ['id' => $theirs->id]);
        // 1000 + 50 + 30
        $this->seeInDatabase('players', ['id' => $me->id, 'credits' => 1080]);
    }
}
