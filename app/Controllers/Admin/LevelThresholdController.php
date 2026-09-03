<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LevelThresholdModel;
use CodeIgniter\HTTP\ResponseInterface;

class LevelThresholdController extends BaseController
{
    private $levelThresholdModel;
    protected $layout = 'back';

    public function __construct(){
        $this->levelThresholdModel = model(LevelThresholdModel::class);    }

    public function index()
    {
        helper('form');
        $this->title = "Courbe des niveaux";

        // Récupère tous les thresholds depuis la base de données
        $levelThresholds = $this->levelThresholdModel->findAll();

        // Envoie les thresholds à la vue pour affichage
        return $this->render('admin/level-thresholds/index', ['levelThresholds' => $levelThresholds]);
    }
    public function new()
    {
        helper('form');
        return $this->render('admin/level-thresholds/form');
    }

    public function insert(){
        $data = $this->request->getPost();
        if(!isset($data['id'])){
        $this->levelThresholdModel->insert($data);
        }else{
            $this->error("Le palier existe déjà");
        }
        return $this->redirect('/admin/level-threshold');
    }
    public function delete(){
        $data = $this->request->getPost();
        if(isset($data['id']))
        {
        $this->levelThresholdModel->delete($data['id']);
        }else{
            $this->error("Il n'y a pas d'ID");
        }
        return $this->redirect('/admin/level-threshold');
    }
    public function update(){
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if($this->levelThresholdModel->update($id, $data)){
            $this->success('Niveau modifié avec succès');
        }else{
            $this->error('Erreur de la modification du niveau');
        }
        return $this->redirect('/admin/level-threshold');
    }
}