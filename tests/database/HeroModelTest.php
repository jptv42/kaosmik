<?php

namespace Tests\Database;

use App\Entities\Hero;
use App\Entities\Player;
use App\Entities\RarityLevel;
use App\Models\HeroModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class HeroModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    private HeroModel $heroModel;
    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();

        $this->heroModel = new HeroModel();
        $this->player    = $this->createPlayer('joueur1');
    }


    //Création d'un mercenaire
    public function testCanCreateHero()
    {
        $id = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Alpha Test',
            'power'         => 20,
            'cost_credit'   => 100,
        ]);

        $this->assertIsInt($id);
        $this->seeInDatabase('heroes', ['name' => 'Alpha Test', 'power' => 20]);
    }

    //Lire un mercenaire
    public function testCanReadHero(){
        $hero = $this->createHero($this->player,['name'=>'Beta Test']);

        $found = $this->heroModel->find($hero->id);

        $this->assertInstanceOf(Hero::class, $found);
        $this->assertSame('Beta Test', $found->name);
        $this->assertSame($this->player->id,$found->player->id);
    }
    //Mettre à jour un mercenaire
    public function testCanUpdateHero()
    {
        $hero = $this->createHero($this->player);

        $this->heroModel->update($hero->id, ['name' => 'Nouveau Nom', 'power' => 35]);

        $found = $this->heroModel->find($hero->id);
        $this->assertSame('Nouveau Nom', $found->name);
        $this->assertSame(35, $found->power);
    }
    //Les mercenaires sont supprimés définitivement (HeroModel n'a pas de soft delete) : c'est ce que le licenciement utilise.
    public function testCanDeleteHero()
    {
        $hero = $this->createHero($this->player);

        $this->heroModel->delete($hero->id);

        // HeroModel n'utilise pas le soft delete : la ligne disparaît vraiment
        $this->assertNull($this->heroModel->find($hero->id));
        $this->dontSeeInDatabase('heroes', ['id' => $hero->id]);
    }
    //Avant de parler de validation, regardons ce que la base de données refuse déjà toute seule : clés étrangères,
    // colonnes non nulles, colonnes UNSIGNED… C'est le dernier rempart, quoi que fasse le code PHP.
    //Une clé étrangère inexistante
    //Un mercenaire doit appartenir à un joueur qui existe (player_id pointe vers players.id).
    //expectException() se place avant l'instruction qui doit lever l'exception : le test réussit si l'exception est levée.
    public function testCannotCreateHeroForUnknownPlayer()
    {
        $this->expectException(DatabaseException::class);

        $this->heroModel->insert([
            'player_id'     => 99999,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Orphelin',
            'power'         => 10,
            'cost_credit'   => 50,
        ]);
    }
    //Une valeur hors limites, même sans validation
    //La colonne cost_credit est UNSIGNED : un coût négatif est impossible.
    // Ici on coupe volontairement la validation du Model avec skipValidation(true) pour prouver que la base protège aussi.
    public function testDatabaseRejectsNegativeCostEvenWithoutValidation()
    {
        $this->expectException(DatabaseException::class);

        // skipValidation(true) coupe les règles du Model : seule la colonne UNSIGNED peut encore refuser
        $this->heroModel->skipValidation(true)->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Cout Negatif',
            'power'         => 20,
            'cost_credit'   => -5,
        ]);
    }

    //Les champs non autorisés sont ignorés
    //$allowedFields liste les colonnes qu'un Model accepte d'écrire. Tout le reste (ici id) est ignoré :
    // c'est une protection contre les envois de champs inattendus.
    public function testFieldsOutsideAllowedFieldsAreIgnored()
    {
        $hero = $this->createHero($this->player);

        // 'id' n'est pas dans $allowedFields : il ne doit pas être modifié
    $this->heroModel->update($hero->id, ['id' => 5000, 'power' => 99]);

    $this->seeInDatabase('heroes', ['id' => $hero->id, 'power' => 99]);
    $this->dontSeeInDatabase('heroes', ['id' => 5000]);
    }
    //Le nom est requis
    public function testValidationRequiresName()
    {
        $result = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'power'         => 20,
            'cost_credit'   => 100,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('name', $this->heroModel->errors());
    }
    //Le nom doit avoir au moins 3 caractères
    public function testValidationNameMustHaveAtLeastThreeCharacters()
    {
        $result = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Al',
            'power'         => 20,
            'cost_credit'   => 100,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('name', $this->heroModel->errors());
    }
    //La puissance doit être positive
    public function testValidationPowerMustBePositive()
    {
        $result = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Sans Puissance',
            'power'         => 0,
            'cost_credit'   => 100,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('power', $this->heroModel->errors());
    }

    //Le coût ne peut pas être négatif
    public function testValidationCostCannotBeNegative()
    {
        $result = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Cout Negatif',
            'power'         => 20,
            'cost_credit'   => -5,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('cost_credit', $this->heroModel->errors());
    }
//    Des données valides passent
//    Un test de validation doit aussi vérifier le cas qui doit fonctionner, pour s'assurer que les règles ne bloquent pas tout.
    public function testValidDataPassesValidation()
    {
        $id = $this->heroModel->insert([
            'player_id'     => $this->player->id,
            'hero_model_id' => 1,
            'rarity_id'     => 1,
            'name'          => 'Valide',
            'power'         => 20,
            'cost_credit'   => 100,
        ]);

        $this->assertIsInt($id);
        $this->assertEmpty($this->heroModel->errors());
    }
//Résultat attendu :
//•	avant l'ajout des règles : les quatre premiers tests de validation échouent (le test « Valide » passe déjà : c'est normal) ;
//•	après : tous passent.

//    Test des relations
//    Un mercenaire est lié à son joueur, à son modèle et à sa rareté. L'entité Hero charge ces liens à la demande.
    public function testHeroKnowsItsPlayerModelAndRarity()
    {
        $hero = $this->createHero($this->player, ['hero_model_id' => 1, 'rarity_id' => 2]);

        $this->assertSame($this->player->id, $hero->getPlayer()->id);
        $this->assertSame('Cadet de la Flotte', $hero->getHeroModel()->name);
        $this->assertInstanceOf(RarityLevel::class, $hero->getRarity());
        //De base le "Peu commun" était écrit "Peu Commun" le test ne passait pas
        $this->assertSame('Peu commun', $hero->getRarity()->name);
    }

//Test des Timestamps
//Insertion
    public function testTimestampsAreSetOnInsert()
    {
        $hero = $this->createHero($this->player);

        $this->assertNotNull($hero->created_at);
        $this->assertNotNull($hero->updated_at);
    }
    //Modification
    //sleep(1) laisse passer une seconde : sans cela, les deux dates seraient identiques (précision à la seconde).
    public function testUpdatedAtIsModifiedOnUpdate()
    {
        $hero     = $this->createHero($this->player);
        $original = $hero->updated_at->toDateTimeString();

        sleep(1);
        $this->heroModel->update($hero->id, ['power' => 50]);

        $updated = $this->heroModel->find($hero->id)->updated_at->toDateTimeString();
        $this->assertNotEquals($original, $updated);
    }
    //Tests du hook refreshStamina
    //HeroModel possède un hook afterFind : à chaque lecture d'un mercenaire,
    // refreshStamina() calcule l'endurance regagnée et l'enregistre en base.
    // Un « hook » (ou callback) est une méthode que le Model appelle automatiquement à un moment précis (avant/après une insertion, une lecture…).
    //Régénération à la lecture
    public function testFindRegeneratesStaminaAndSavesIt()
    {
        $hero = $this->createHero($this->player, [
            'stamina_current'     => 50,
            'stamina_max'         => 100,
            'last_stamina_update' => Time::now()->subMinutes(10)->subSeconds(5)->toDateTimeString(),
        ]);

        // Le simple fait de lire le héros déclenche la régénération
        $found = $this->heroModel->find($hero->id);

        $this->assertSame(55, $found->stamina_current);
        $this->seeInDatabase('heroes', ['id' => $hero->id, 'stamina_current' => 55]);
    }
//    Pas de modification quand l'endurance est pleine
    public function testFindDoesNotTouchStaminaOfFullHero()
    {
        $hero = $this->createHero($this->player, ['stamina_current' => 100, 'stamina_max' => 100]);

        $found = $this->heroModel->find($hero->id);

        $this->assertSame(100, $found->stamina_current);
    }


}
