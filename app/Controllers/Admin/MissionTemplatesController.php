<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MissionTemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;


class MissionTemplatesController extends BaseController
{
    private $missionModel;
    protected $layout = 'back';
    public function __construct(){
        $this->missionModel = model(MissionTemplatesModel::class);
    }
    public function index(){
        helper('form');
        $this->title = "Missions";
        $missions = $this->missionModel->findAll();
        return $this->render('admin/missions/index', ['missions' => $missions]);
    }
    public function delete($id=null){
        if($id != null){
            $this->missionModel->delete($id);
            $this->success("La mission a été supprimé !");
        }else{
            $this->error("Impossible de supprimer la mission !");
        };
        return $this->redirect('admin/mission');
    }
}
