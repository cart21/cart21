<?php
class Index extends CI_Controller {
 
    function __construct() {
        parent::__construct();
        
        $this->load->model("product_model");
         
    }
 
    function index() {
    	
        $this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
     
        
        $this->smarty->view('index',$this->data);
 	}
 	
 	function page() {
 		 
 		$this->quick->Header("");
 		$this->quick->Top_menu("");
 		$this->quick->Footer("");
 		 
 	
 		$this->smarty->view('index',$this->data);
 	}
 	
 	function newsletters(){
 		
 		
 		
 		if( filter_var($this->input->post("email"),FILTER_VALIDATE_EMAIL)===false){
 			
 			echo $this->language_model->language_c_key("invalid")." ".$this->language_model->language_c_key("email");
 			
 		}else{
 			
 			$this->db->query(" replace into  newsletter (email) values('".$this->input->post("email")."')");
 			echo $this->language_model->language_c_key("producttext3");
 		}
 		exit;
 	}

   
}