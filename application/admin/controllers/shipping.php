<?php
class shipping extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        $this->load->model("order_model");
    }
 
    
    function index() {
    	$this->permission->check_permission("view");
    	

    	$this->data["companies"]=$this->order_model->shipping_companies();
    	
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    $this->smarty->view('management/shipping',$this->data);
    }
    
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("company_id")>0){
    
    		$this->db->where("company_id",$this->input->post("company_id"))->update("shipping_company",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function add(){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("add");
    	
    	$this->data["page"]="add";
    	
    	if( $this->input->post() and $this->permission->check_permission("add") ){
    		 
    		$this->load->helper(array('form', 'url'));
    		$this->load->library('form_validation');
    		 
    		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|xss_clean');
    		 
    		if ($this->form_validation->run() 	) {
    	
    			$sh["company_name"]= $this->input->post("company_name");
    			$sh["price"]= $this->input->post("price");
    			 
    			$this->db->insert("shipping_company",$sh);
    			
    			$this->quick_model->logs("new shipping company added");
    			redirect("/admin/shipping");
    		}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
    		}
    		 
    	}else {
    		$verrors=array_filter(explode('.',validation_errors()));
    		foreach($verrors as $verror){
    			$this->quick->errors[] = strip_tags($verror).".";
    		}
    	}
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('management/shipping_form',$this->data);
    }
    
    function edit($id){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	
    	$this->data["page"]="edit";
    	
    	
    	if( $this->input->post() and $this->permission->check_permission("edit") ){
    		 
    		$this->load->helper(array('form', 'url'));
    		$this->load->library('form_validation');
    		 
    		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required|xss_clean');
    		 
    		if ($this->form_validation->run() 	) {
    			 
    			$sh["company_name"]= $this->input->post("company_name");
    			$sh["price"]= $this->input->post("price");
    			
    			$this->db->where("company_id",$id)->update("shipping_company",$sh);
    			$this->quick->success[]="Başarıyla editlendi";
    			$this->quick_model->logs("shipping company ".$id."edited");
    		}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
    	
    	}
		}
		

		$this->data["POST"]=$this->order_model->shipping_company($id)->row_array();
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('management/shipping_form',$this->data); 
    }
    
    function delete(){
    
    	if($this->permission->check_permission("delete") ){
    			
    		$this->db->where_in("company_id",$this->input->post("company_id"))->delete("shipping_company");
    		$this->quick_model->logs(" shipping company ".$this->input->post("company_id")." deleted ");
    		$result=$this->input->post("company_id");
    	}else{
    		$result="0";
    	}
    	echo $result;
    	exit;
    }
    
    
    function sort_order_bank(){
    
    
    	if($this->input->is_ajax_request()){
    		 
    		$type_ids=explode(',',$this->input->post("type_ids"));
    		 
    		$i=1;
    		foreach($type_ids as $type_id){
    
    			$this->db->where("company_id",$type_id)->update("shipping_company",array("sort_order"=>$i));
    
    			$i++;
    		}
    		$this->quick_model->logs("company sorted");
    		exit;
    	}
    }
    
     
		
}