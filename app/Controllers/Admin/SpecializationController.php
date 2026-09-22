<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SpecializationModel;
use codeIgniter\HTTP\ResponseInterface;

class SpecializationController extends BaseController
{
    protected $specializationModel;
    protected $layout = 'back';

    public function __construct(){
        $this->specializationModel = model("SpecializationModel");
    }
    public function index()
    {
        helper('form');
        $specializations = $this->specializationModel->findAll();
        return $this->render('/admin/specialization/index', ['specializations'=>$specializations]);
    }

    public function delete(){
        $data = $this->request->getPost();
        if(isset($data['id']))
        {
            $this->specializationModel->delete($data['id']);
            $this->success("Spécialisation effacé avec succés !");
        }else{
            $this->error("Il n'y a pas d'ID");
        }
        return $this->redirect('/admin/specialization');
    }
    public function update(){
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if($this->specializationModel->update($id,$data)){
            $this->success('La spécialisation a été modifié !');
        }else{
            $this->error('La modification a échoué !');
        }
        return $this->redirect('/admin/specialization');
    }
    public function create(){
        $data = $this->request->getPost();
        if(!isset($data['id'])){
            $this->specializationModel->insert($data);
            $this->success('La spécialisation a bien été créée !');
        }else{
            $this->error('La spécialisation n\'a pas été créée!');
        }
        return $this->redirect('/admin/specialization');
    }
}
