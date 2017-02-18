<?php
class tax extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();

        $this->load->model("order_model");
    }
 
    
    function index() {
    	$this->permission->check_permission("view");
    
    	$this->data["taxes"]=$this->order_model->taxes();
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    $this->smarty->view('management/tax',$this->data);
    }
    
 	function add(){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("add");
    	$this->quick->Header("");
    	$this->data["page"]="add";
    	
    	if($this->input->is_ajax_request() and $this->input->post() ) {
    	
    	$this->db->insert("tax",$this->input->post() );
    	echo 1; exit;
    	}
    	 
    	 $this->smarty->view('management/tax_form',$this->data); exit;
    }
     

    function edit($id){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->quick->Header("");
    	$this->data["page"]="edit";
    	
    	if($this->input->is_ajax_request() and $this->input->post() ) {
    		 
    		$this->db->where("tax_id",$id)->update("tax",$this->input->post() );
    		echo 1; exit;
    	}
    	
    	$this->data["POST"]=$this->order_model->tax($id)->row_array();
    	 
    	  $this->smarty->view('management/tax_form',$this->data); exit;
    }
    
    function  delete($id){
    	$this->permission->check_permission("delete");
    
    	$this->db->where("tax_id",$id)->delete("tax");
    	echo $id; exit;
    }
    
    
    
    
     
		
}