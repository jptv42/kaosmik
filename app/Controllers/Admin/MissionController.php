<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MissionModel;
use CodeIgniter\HTTP\ResponseInterface;


class MissionController extends BaseController
{
    private $missionModel;
    protected $layout = 'back';
    public function __construct(){
        $this->missionModel = model(MissionModel::class);
    }
    public function new(){
        helper('form');
        $missions = $this->missionModel->findAll();
        return $this->render('admin/missions/form', ['missions' => $missions]);
    }
    public function index(){
        helper('form');
        $missions = $this->missionModel->findAll();
        return $this->render('admin/missions/index', ['missions' => $missions]);
    }
    public function edit($id = null) {
        if($id != null) {
            $missionmodel = $this->missionModel->find($id);
            if($missionmodel) {
                helper('form');
                return $this->render('admin/missions/form', ['m' => $missionmodel]);
            }
        }
        $this->error('Aucune mission trouvé');
        return $this->redirect('/admin/mission');
    }
    public function createUpdate() {
    $missionmodeldata = $this->request->getPost();
    $missionmodel = new MissionModel();
    $saveOK = $this->missionModel->save($missionmodeldata);
    if($saveOK){
        if(isset($missionmodeldata['id'])){
            $this->success('La mission '.$missionmodeldata['title'].' est modifié');
            $id = $missionmodeldata['id'];
        }else{
            $this->success('La mission '.$missionmodeldata['title'].' est créé');
            $id = $this->missionModel->getInsertID();
        }
        return $this->redirect('admin/mission/edit/' . $id);
    }
        $this->error('Une erreur est survenue');
        return $this->redirect('/admin/mission');
    }
    public function delete($id=null){
        if($id != null){
            $this->missionModel->delete($id);
            $this->success("La mission a été supprimé !");
        }else{
            $this->error("Impossible de supprimer la mission !");
        };
        return $this->redirect('/admin/mission');
    }
}
