<?php

namespace app\tests\unit\Helpers;

use App\Entities\Hero;
use App\Models\PlayerModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;
use Tests\Database\Services;
final class HelpersTests extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;
    public function testRecruitCreatesHeroAndChargesPlayer()
    {
        $player = $this->createPlayer('joueur1');
        $offer  = $this->service->generateOffers($player->id)[0];

        $hero = $this->service->recruit($offer->id);

        $this->assertInstanceOf(Hero::class, $hero);
        $this->assertSame($player->id, $hero->player_id);
        $this->assertSame(1000 - $offer->cost_credit, (new PlayerModel())->find($player->id)->credits);
    }

}
