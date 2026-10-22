<?php
namespace Tests\Feature\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\Traits\CreatesPlayers;
final class AuthControllerTest extends CIUnitTestCase{
    use DatabaseTestTrait;//Donne accès à la base de test et aux assertions seeInDatabase()…
    use FeatureTestTrait;//Permet d'écrire $this->get() et $this->post()
    use AuthenticationTesting;//Fournit actingAs($user) : connecter un utilisateur sans passer par le formulaire
    use CreatesPlayers;//Notre trait de création de joueurs

    protected $migrate = true;//Crée les tables avant les tests
    protected $refresh = true;//Remet la base à zéro avant chaque test (tables supprimées puis recréées)
    protected $DBGroup = 'tests';//Utilise la base de test
    protected $namespace = null;//Joue toutes les migrations, y compris celles de Shield (sinon les tables users, auth_identities… n'existeraient pas)
    protected $seed = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';//Seeder joué avant chaque test

    //Réinitialise les services et installe une fausse session, pour que chaque test parte d'un visiteur non connecté
    protected function setUp():void{
        parent::setUp();
        // Repartir d'une session vide : sinon l'utilisateur d'un test précédent reste connecté
        $this->resetServices();
        $this->mockSession();
    }
    //Insérer vos tests ici

    //Tester une page publique
    public function testHomePageLoads(){
        $result = $this->get('/');
        $result->assertStatus(200);
    }
    //Tester la connexion (Login)
    public function testLoginPageIsAccessible(){
        $result = $this->get('/login');
        $result->assertStatus(200);
        $result->assertSee('Connexion');
    }
    //Tester la connexion avec des données valides
    public function testLoginWithValidCredentials(){
        $this->createPlayer('joueur1');
        $result = $this ->post('/login',[
          'email' =>'joueur1@kaosmik.test',
          'password'=>'Kaosmik2026!',
        ]);
        //rtrim(site_url(), '/') : Shield redirige vers l'accueil sans / final (http://example.com), alors que site_url() en ajoute un.
        $result->assertRedirectTo(rtrim(site_url(),'/'));
        $this->assertTrue(auth()->loggedIn());
    }
    //Tester la connexion avec des données invalides(email)
    public function testLoginWithUnknownEmail(){
        $result = $this->post('/login',[
        'email'=>'inconnu@kaosmik.test',
        'password'=>'Kaosmik2026!',]);
        $result->assertRedirect();
        $result->assertSessionHas('error','Unable to log you in. Please check your credentials.');
    }
    //Tester la connexion avec des données invalides (password)
    public function testLoginWtihWrongPassword(){
        $this->createPlayer('joueur1');
        $result = $this->post('/login',[
            'email'=>'joueur1@kaosmik.test',
            'password'=>'MauvaisMotDePasse1!',
        ]);
        $result->assertRedirect();
        //Remarquez que le message n'est pas le même selon que l'email existe ou non.
        // C'est un comportement de Shield qui permet à un attaquant de savoir quels emails ont un compte. Gardez cette observation :
        // elle vous resservira en cours de sécurité.
        $result->assertSessionHas('error','Unable to log you in. Please check your password.');
        $this->assertFalse(auth()->loggedIn());
    }
    //Tester la connexion avec des données invalides (vide)
    //Ici, c'est la validation du formulaire qui refuse la requête.
    // Les messages sont donc rangés sous la clé errors (au pluriel), un par champ.
    public function testLoginWithEmptyCredentials(){
        $result = $this->post('/login',[
            'email'=>'',
            'password'=>'',
        ]);
        $result->assertRedirect();
        $result->assertSessionHas('errors',[
            'email'=>'The Email Address field is required.',
            'password'=>'The Password field is required.',
        ]);
    }
    //Tester l'inscription
    public function testRegisterCreatesUserAndPlayer(){
        $result = $this->post('/register',[
            'username' =>'nouveau',
            'email'=>'nouveau@kaosmik.test',
            'password'=>'Kaosmik2026!',
            'password_confirm'=>'Kaosmik2026!',
        ]);
        $result->assertRedirectTo(rtrim(site_url(),'/'));
        $this->seeInDatabase('users',['username'=>'nouveau']);
        $user = model('UserModel')->findByCredentials(['email'=>'nouveau@kaosmik.test']);
        $this->seeInDatabase('players',['user_id'=>$user->id]);
    }
    //Tester la déconnexion
    //actingAs() connecte directement le joueur, sans passer par le formulaire : on teste ainsi la déconnexion seule.
    public function testLogoutWorks(){
        $player = $this->createPlayer('joueur1');
        $this->actingAs($this->getUserOf($player));

        $result = $this->get('/logout');

        $result->assertRedirectTo(site_url('/login'));
        $this->assertFalse(auth()->loggedIn());
    }

    //Tester si on a accès aux pages joueur sans être connecté
    public function testCantinaRequiresAuthentication()
    {
        $result = $this->get('/cantina');

        $result->assertRedirectTo(site_url('/login'));
    }


    //Tester l’accès à l’admin sans être administrateur
    public function testAdminPageRequiresAdminGroup()
    {
        $player = $this->createPlayer('joueur1');
        $this->actingAs($this->getUserOf($player));

        $result = $this->get('/admin');

        $result->assertRedirect();
        $result->assertSessionHas('error');
    }
    //Tester l’accès à l’admin en étant administrateur
    public function testAdminPageIsAccessibleForAdmin()
    {
        $player = $this->createPlayer('admin');
        $user   = $this->getUserOf($player);
        $user->addGroup('admin');
        $this->actingAs($user);

        $result = $this->get('/admin');

        $result->assertStatus(200);
    }
}