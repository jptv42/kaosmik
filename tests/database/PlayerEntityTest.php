<?php

namespace Tests\Database;

use App\Entities\Player;
use App\Models\PlayerModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class PlayerEntityTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    //Retrouver un joueur à partir de son compte
    public function testFindByUserIdReturnsThePlayer(){
        $player = $this->createPlayer('joueur1');

        $found = (new PlayerModel())->findByUserId($player->user_id);

        $this->assertInstanceOf(Player::class, $found);
        $this->assertSame($player->id, $found->id);
    }

    public function testFindByUserIdReturnsNullForUnknownUser(){
        $this->assertNull((new PlayerModel())->findByUserId(99999));
    }

    //Le niveau selon l'expérience
    //Les seuils viennent de LevelThresholdSeeder : niveau 2 à 100 d'expérience, niveau 3 à 230.
    public function testCheckLevelReadsTheThresholdsTable()
    {
        $player = new Player();

        $this->assertSame(1, $player->checkLevel(0));
        $this->assertSame(1, $player->checkLevel(99));
        $this->assertSame(2, $player->checkLevel(100));
        $this->assertSame(3, $player->checkLevel(230));
    }

    //Le niveau selon l'expérience
    //Les seuils viennent de LevelThresholdSeeder : niveau 2 à 100 d'expérience, niveau 3 à 230.
    //Tester les valeurs limites (99 et 100) est une bonne habitude : c'est souvent là que se cachent les erreurs.
    public function testSetExperienceUpdatesTheLevel()
    {
        $player = new Player();

        $player->experience = 100;

        $this->assertSame(100, $player->experience);
        $this->assertSame(2, $player->level);
    }
    //La flotte et sa puissance
    public function testFleetIsFullWhenCapacityIsReached(){
        $player = $this->createPlayer('joueur1',['fleet_capacity'=>2]);
        $this->createHero($player);

        $this->assertFalse($player->isFleetFull());

        $this->createHero($player);
        $fresh = (new PlayerModel())->find($player->id);

        $this->assertTrue($fresh->isFleetFull());
    }

    public function testTotalPowerIsTheSumOfHeroesPower(){
        $player = $this->createPlayer('joueur1');
        $this->createHero($player, ['power'=>10]);
        $this->createHero($player, ['power'=>15]);

        $this->assertCount(2, $player->getHeroes());
        $this->assertSame(25, $player->getTotalPower());
    }
    //Dans le premier test, on recharge le joueur ($fresh) car
    // l'entité Player garde en mémoire la liste de ses mercenaires une fois qu'elle l'a lue.

}
