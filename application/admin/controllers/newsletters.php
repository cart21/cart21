<?php

class newsletters extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       $this->load->model('adminuser_model');
    }
 
    
    function index() {
  
		$this->permission->check_permission("view");
		
		$this->load->library('form_validation');
		$this->load->helper('phpmailer');
		$this->load->library("email_template");
		
		
		if( $this->input->post() and $this->permission->check_permission("edit")  ){
			
			
		$this->form_validation->set_rules('email_to', 'email to', 'required|xss_clean');	
		$this->form_validation->set_rules('subject', 'subject ', 'trim|required|xss_clean');
		$this->form_validation->set_rules('body', 'body ', 'trim|required|xss_clean');
		
			if($this->form_validation->run() 	){
			
				
			/// input emails
			$this->data["POST"]["emails"]=$this->input->post("email_to");
				
			foreach($this->data["POST"]["emails"] as $email_to){
			
				SendMailWithGmailSMTP($email_to,$this->input->post("subject"), $this->input->post("body"));   //// sending
			}	
			///
			
			/// newsletter sending
			if($this->input->post("newsletter")){
			$newsletters=$this->db->get("newsletter");
			
			if($newsletters->num_rows){
				foreach ($newsletters->result_array() as $news_email){
					
					SendMailWithGmailSMTP($news_email,$this->input->post("subject"), $this->input->post("body")); //// sending
				}
			}
			}
			///
			
			$this->quick->success[]=$this->language_model->language_c_key("newslettertext1");
			}
			else{
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
			}
			
		}
		
		
		$this->data["customer_groups"]=$this->adminuser_model->customer_groups()->result_array();
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
    $this->smarty->view('newsletters',$this->data);
 
    }
    
    function email_list(){
    	
    	$this->permission->check_permission("view");
    	
    	$this->data["email_list"]=$this->db->get("newsletter");
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    $this->smarty->view('newsletter_list',$this->data);
    }
    
    function remove_email(){
    	$this->permission->check_permission("view");
    	
    	if($this->permission->check_permission("delete")){
    		
    		$this->db->where_in("email",$this->input->post("email"))->delete("newsletter");
    		echo "1";
    	}else{
    		echo "0";
    	}
    	exit;
    
    }
		
}