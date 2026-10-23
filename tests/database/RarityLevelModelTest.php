<?php
//P.42


namespace Tests\Database;

use App\Entities\RarityLevel;
use App\Models\RarityLevelModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class RarityLevelModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $refresh = true;
    protected $DBGroup = 'tests';
    protected $namespace = null;
    protected $seed = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    private RarityLevelModel $rarityModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rarityModel = new RarityLevelModel();
    }

    private function newRarity(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test',
            'color' => '#123456',
            'power_multiplier' => '1.2',
            'cost_multiplier' => '1.2',
            'appearance_rate' => '5',
        ], $overrides);
    }
    //La fonction newRarity() fabrique une rareté valide ; chaque test ne change que ce qui l'intéresse.
    //Les données du seeder
    public function testSeededRaritiesAreAvailable()
    {
        // 1 rareté par défaut (migration) + 5 raretés (seeder)
        $this->assertCount(6, $this->rarityModel->findAll());
    }
    //Le taux de « Commun » est le complément des autres
    //assertEqualsWithDelta() compare des nombres décimaux avec une petite tolérance, pour éviter les erreurs d'arrondi.
    public function testCommonRateIsTheRemainderOfTheOthers()
    {
        // Seeder : 12 + 8 + 4 + 2 + 1 = 27, donc Commun = 73
        $common = $this->rarityModel->find(1);

        $this->assertEqualsWithDelta(73.0, $common->appearance_rate, 0.001);
    }

    //Traduction de la virgule
    public function testConvertAppearanceRateAcceptsComma()
    {
        $id = $this->rarityModel->insert($this->newRarity(['appearance_rate' => '3,5']));

        $this->assertEqualsWithDelta(3.5, $this->rarityModel->find($id)->appearance_rate, 0.001);
    }

    //Recalcul après insertion
    public function testCommonRateIsRecalculatedAfterInsert()
    {
        $this->rarityModel->insert($this->newRarity(['appearance_rate' => '10']));

        // 73 - 10 = 63
        $this->assertEqualsWithDelta(63.0, $this->rarityModel->find(1)->appearance_rate, 0.001);
    }

    //Recalcul après suppression
    public function testCommonRateIsRecalculatedAfterDelete()
    {
        // La rareté 3 ("Rare") pèse 8 % : sa suppression rend 8 % à Commun
        $this->rarityModel->delete(3);

        $this->assertEqualsWithDelta(81.0, $this->rarityModel->find(1)->appearance_rate, 0.001);
    }

    //Le taux de « Commun » ne devient jamais négatif
    public function testCommonRateNeverGoesBelowZero()
    {
        $this->rarityModel->insert($this->newRarity(['appearance_rate' => '90']));

        $this->assertEqualsWithDelta(0.0, $this->rarityModel->find(1)->appearance_rate, 0.001);
    }

    //Impossible de supprimer « Commun »
    public function testCannotDeleteDefaultRarity()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Interdiction de modifier ou supprimer la rareté par défaut (commun)');

        $this->rarityModel->delete(1);
    }

//Tirage d'une rareté
    public function testGetRandomRarityReturnsARarity()
    {
        $rarity = $this->rarityModel->getRandomRarity();

        $this->assertInstanceOf(RarityLevel::class, $rarity);
    }
    //Impossible de modifier « Commun »
    //Le message d'erreur du hook, ainsi que son commentaire dans le code, parlent de « modifier ou supprimer » la rareté par défaut.
    public function testCannotUpdateDefaultRarity()
    {
        $this->expectException(\Exception::class);

        $this->rarityModel->update(1, ['color' => '#000000']);
    }
//Exercice : normalement, ce test échoue (« Failed asserting that exception of type "Exception" is thrown »).
// Le test est cohérent avec ce que le code annonce : c’est le code qui est incomplet. Essayez de modifier RarityLevelModel pour qu’il passe.
// Ne modifiez pas le test.
//Correction possible : le hook protectDefaultRarity existe déjà, mais il n'est branché que sur la suppression.
// Dans la section // Callbacks de app/Models/RarityLevelModel.php, branchez-le aussi avant une modification :
//protected $beforeUpdate   = ['convertAppearanceRate', 'protectDefaultRarity'];
//Lancez ensuite tout le fichier : le test passe, et les autres aussi
// (le recalcul du taux de « Commun » met à jour la table sans repasser par le Model, il n'est donc pas bloqué par ce hook).

}