<?php
class payment extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       $this->load->model("order_model");
    }
 
    
    function index() {
    	$this->permission->check_permission("view");
    
    	
    	$this->data["order_status"]=$this->order_model->order_status_all();
    	
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    $this->smarty->view('management/payment',$this->data);
    }
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("order_status_id")>0){
    
    		$this->db->where("order_status_id",$this->input->post("order_status_id"))->update("order_status",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }
    
  
    
    function detail($id,$key){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->quick->Header("");
    
    	$this->data["ci"]=& get_instance() ;
    	
    	if($key=="banktransfer"){
    
    		$this->data["banks"]=$this->order_model->banks()->result_array();
    
    		echo  $this->smarty->fetch('payment/bank.tpl',$this->data); exit;
    
    	}
    	/// paymet plugin find ///
    	
    	elseif($key=="creditcart"){
    
    		echo  $this->smarty->fetch('payment/creditcart.tpl',$this->data); exit;
    	}elseif($key=="banktransfer"){
    
    		$this->data["shipping_company"]=$this->order_model->shipping_companies();
    
    		echo  $this->smarty->fetch('payment/shipping_company.tpl',$this->data); exit;
    	}
    	else{
    		echo "dd"; exit;
    	}
    
    	return true;
    }

    function add_bank(){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("add");
    	 
    	 $this->smarty->view('payment/bank_form',$this->data); exit;
    }
     

    function edit_bank($id){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->quick->Header("");
    	
    	$this->data["POST"]=$this->order_model->bank($id)->row_array();
    	 
    	  $this->smarty->view('payment/bank_form',$this->data); exit;
    }
    
    function save_bank(){

    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->permission->check_permission("add");
    	

    	if($this->input->is_ajax_request() and $this->input->post() and $this->permission->check_permission("edit") ) {
    		
    		if($this->input->post("bank_id")){
    			$this->db->where("bank_id",$this->input->post("bank_id"))->update("bank",$this->input->post() );
    		}else{
    			
    			$this->db->insert("bank",$this->input->post() );
    		}
    		
    	}
    	 echo 1; exit;
    }
    
   function  delete_bank($id){
   	$this->permission->check_permission("delete");
   	
	   	$this->db->where("bank_id",$id)->delete("bank");
	   	echo $id; exit;
    }
    
    function sort_order_bank(){
    		
    		
    	if($this->input->is_ajax_request()){
    			
    		$type_ids=explode(',',$this->input->post("type_ids"));
    			
    		$i=1;
    		foreach($type_ids as $type_id){
    				
    			$this->db->where("order_status_id",$type_id)->update("order_status",array("sort_order"=>$i));
    				
    			$i++;
    		}
    		//$this->quick_model->logs("bank sorted");
    		exit;
    	}
    }
     

    
     
		
}