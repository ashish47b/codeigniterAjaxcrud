<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemsController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->model('ItemModel');
    }
    
    public function index() { 
         $this->load->view('items_list');
    }
    public function getItems() { 
        $items = $this->ItemModel->getItems();
         echo json_encode($items);
    }
    
    public function createItem() { 
          echo "<pre>"; 

        $uploaded_files = array();
        $errors = array();
        if(isset($_FILES['images']) && !empty($_FILES['images'])) {
      for ($i = 0; $i < count($_FILES['images']['name']); $i++) { 
                $filename = $_FILES['images']['name'][$i];
                $tmpname = $_FILES['images']['tmp_name'][$i];
                $type = $_FILES['images']['type'][$i];
                $error = $_FILES['images']['error'][$i];
                $size = $_FILES['images']['size'][$i];
                $exp = explode('.', $filename);
                $ext = end($exp);
                $newname = $exp[0] . '_' . time() . "." . $ext; 
                $config['upload_path'] = './assets/uploads/';
                $config['upload_url'] = base_url() . 'assets/uploads/';
                $config['allowed_types'] = "gif|jpg|jpeg|png|ico|JPG";
                $config['max_size'] = '20000';
                $config['file_name'] = $newname;
                $config['encrypt_name'] = TRUE;
                $this->load->library('upload', $config);
                if(in_array('',$exp)){
                   $newname=$this->input->post('imagesOld')[$i];
                }else{
                    if(isset($this->input->post('imagesOld')[$i])){
                         unlink('./assets/uploads/'.$this->input->post('imagesOld')[$i]);
                         $newname = $exp[0] . '_' .time() . "." . $ext;
                         $config['file_name'] = $newname;
                         move_uploaded_file($tmpname, "./assets/uploads/" . $newname);
                    }else{
                          $newname = $exp[0] . '_' .time() . "." . $ext;
                         $config['file_name'] = $newname;
                         move_uploaded_file($tmpname, "./assets/uploads/" . $newname);
                    }
                  
                 
                }
              $uploaded_files[] = $newname;
             }
            } else {
    // Handle the case where no files were uploaded
            echo "No files were uploaded.";
            }
            $data['name']=json_encode($this->input->post('name'));
            $data['description']=json_encode($this->input->post('description'));
            $data['price']=json_encode($this->input->post('price'));
            $data['image_url'] = json_encode($uploaded_files); 
        
        if(isset($_POST['id'])){
            $id= $_POST['id'] ;
            $result = $this->ItemModel->updateItem($id, $data);
        }else{
          $result = $this->ItemModel->createItem($data);
        }
       
        if($result){
            $responce = ['status'=>200,'massege'=>'Successfully Save'];
        }else{
             $responce = ['status'=>500,'massege'=>'Something Wrong'];
        }
        
        echo json_encode($responce);
        die;
    }
    
   
    
    public function deleteItem() {
        $id =$_POST['id'];
        $result = $this->ItemModel->deleteItem($id);
        echo json_encode($result);
    }
    public function singleItem() {
        $id =$_POST['id'];
        $result = $this->ItemModel->singleItem($id);
        echo json_encode($result);
    } 
    
    public function finalSubmit() {
         $id= $_POST['id'] ;
         $data['final_submit'] = 1;
         $result = $this->ItemModel->updateItem($id, $data);
    }
}