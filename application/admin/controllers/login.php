<?php
class login extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
        $this->load->model('quick_model');
    }
 
    
    function index() {
    
       $this->data["errors"]="";
      
        $this->data["dd"]="dd";
     	$this->load->library('form_validation');
        
		$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean|callback_email_exist');
    	$this->form_validation->set_rules('password', 'şifre', 'trim|required|xss_clean');	
    	
    	$this->data["POST"]=$this->input->post();
		if(  $this->input->post() && !$this->quick->logged_in()&& $this->form_validation->run() ){
		
				$this->load->model('Account_Model');
				
				$email=$this->db->escape($this->input->post("email",true));
				$password=sha1($this->input->post("password",true));
				$customer=$this->Account_Model->login($email,$password );
				if($customer->num_rows()>0){
			
					$pattern=array(
					"customer_id" =>"",
					"firstname" =>"",
					"lastname" =>"",
					"email" =>"",
					"image_url" =>""
					);
				
				$sesion=array_intersect_key($customer->row_array(),$pattern);
				
				$this->sessiondd->set_userdata('admin',$sesion);
				$this->quick_model->logs($customer->row()->customer_id." id logged in  ");
			
				}else {
					$this->data["errors"][] = "wrong data";
					$this->quick_model->logs($this->input->post("email")." email try to login unsuccesfully  ");
				}
		
			//echo 'ddddddf';
		}else {
			   $verrors=array_filter(explode('.',validation_errors()));
			   foreach($verrors as $verror){
			   $this->data["errors"][] = strip_tags($verror).".";
			   }
		
		}
	
		
	
	
		$this->quick->Header("");
		$this->quick->Footer("");
		
		if($this->quick->logged_in()) {
	
			redirect('admin/dashboard');
		
		}else{
		
		$this->smarty->view('account/login',$this->data);
		}
		
    
 
    }
    
    
    function email_exist($email){
    	
    	
    	$customer=$this->db->where("email",$email)->get("admin");
    	
    	if($customer->num_rows>0){
    	
    	return true;
    	}else{
    	
    	 $this->data["errors"][] =" wrong data ";
    	return false;
    	}
    
    }
    
     
		
}