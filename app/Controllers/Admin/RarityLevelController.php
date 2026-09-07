<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RarityLevelModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\RarityLevel;
class RarityLevelController extends BaseController
{
    private $rarityLevelModel;
    protected $layout = 'back';

    public function __construct(){
        $this->rarityLevelModel = model(RarityLevelModel::class);}


    public function index()
    {
        helper('form');
        $this->title = "Niveau de rareté";

        // Récupère toute les raretés depuis la base de données
        $rarityLevels = $this->rarityLevelModel->findAll();

        // Envoie les raretés à la vue pour affichage
        return $this->render('admin/rarity-levels/index', ['rarityLevels' => $rarityLevels]);
    }
    public function insert(){
        $data = $this->request->getPost();
        if(!isset($rarityLevel->id)){
            $this->rarityLevelModel->insert($data);
            $this->success("Insertion réussi !");
        }else{
            $this->error("Rareté deja existante !");
        }
        return $this->redirect('admin/rarity-level');
    }
    public function delete(){
        try {
            $id = $this->request->getPost('id');
            if ($id == 1) {
                $this->error("Impossible de supprimer la rareté par défaut (commun)");
                return $this->redirect('admin/rarity-level');
            }
            $deleteOk = $this->rarityLevelModel->delete($id);
            if($deleteOk){
                $this->success('Rareté supprimée');
            } else {
                $this->error('Une erreur est survenue');
            }
        } catch (\Exception $e){
            $this->error($e->getMessage());
        }
        return $this->redirect('admin/rarity-level');
    }

    public function update()
    {
        $id = $this->request->getPost("id");

        //  Récupération de l'entité existante
        $rarityLevel = $this->rarityLevelModel->find($id);

        if (!$rarityLevel){
            $this->error('Rareté introuvable');
            return $this->redirect('/admin/rarity-level');
        }

        //  Remplissage avec les données postées
        $rarityLevel->fill($this->request->getPost());


        //  Enregistrement (CodeIgniter fait un UPDATE)
        if ($this->rarityLevelModel->save($rarityLevel)) {
            $this->success('Rareté modifiée avec succès');
        }else{
            $this->error('Erreur lors de la modification de la rareté');
        }

        return $this->redirect('/admin/rarity-level');
    }
}
