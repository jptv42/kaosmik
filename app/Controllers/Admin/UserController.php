<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\Player;
use App\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $layout = "back";
    private $userModel;
    private $playerModel;

    public function __construct()
    {
        $this->userModel = model("UserModel");
        $this->playerModel = model("PlayerModel");
    }
    public function index()
    {
        helper('form');
        $this->title = "Liste des utilisateurs";
        $users = $this->userModel->findAll();
        return $this->render('admin/user/index', ['users' => $users]);
    }

    public function edit($id = null)
    {
        helper('form');
        $user = $this->userModel->find($id);
        return $this->render('admin/user/form', ['user' => $user]);
    }

    public function update() {
        $data = $this->request->getPost();
        //On vérifie qu'on à bien un ID
        if(!isset($data['id'])) {
            $this->error('Identifiant inconnu');
            $this->redirect('/admin/user');
        }
        $user_id = $data['id'];
        unset($data['id']);

        //Récupération des objets (entité)
        $user = $this->userModel->find($user_id);
        $player = $user->getPlayer();

        //On vérifie si on à le champs active sinon on le met à 0
        if(isset($data['active']) && $data['active'] == 'on') {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }
        //On rempli nos objets
        $user->fill($data);
        $player->fill($data);
        //On sauvegarde en BDD
        $this->userModel->save($user);
        $this->playerModel->save($player);
        //On affiche un message de réussite
        $this->success($user->username . " a bien été modifié.");
        //On redirige
        return $this->redirect('/admin/user/edit/'. $user_id);
    }
    public function new(){
        helper('form');
        $data = $this->request->getPost();
        //Récupération des objets (entité)
        $user = new User();
        $player = new Player();
        //On rempli nos objets
        $user->fill($data);
        // Sauvegarde de l'utilisateur d'abord
        $this->userModel->save($user);
         // Sauvegarde du joueur avec sa liaison
        $this->playerModel->save($player);
        //On affiche un message de réussite
        $this->success($user->username . " a bien été créé.");
        $this->title = "Créer un utilisateur";
        //On redirige
        return $this->render('admin/user');
    }
}