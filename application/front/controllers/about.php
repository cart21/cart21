<?php

class about extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    function index() {

        if(  $this->input->post()  ){
        
        $this->data["POST"]= $this->input->post();
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    	$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|xss_clean');
    	//$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('subject', 'Subject', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
    	
    	 if( $this->form_validation->run() ){	
    		
    		
    		$this->load->library('CIphpmailer');
    	
        	$text_message="";
        	$text_message.='<p> Subject :'.$this->input->post("subject").'</p>';
			$text_message.='<p> messsage :'.$this->input->post("message").'</p>';
			//$text_message.='<p> tel :'.$this->input->post("tel").'</p>';
			$text_message.='<p> email :'.$this->input->post("email").'</p>';
			
			$r=$this->ciphpmailer->SendMailWithSMTP($this->data["settings"]["email"],$this->data["settings"]["site_title"].' - '.$this->input->post("subject").' Contact message',$text_message,$From="");	
    		
    		if($r){
    			$this->quick->success[] =$this->language_model->language_c_key("newslettertext1");
				$this->data["POST"]="";
    		}else{
    		$this->quick->errors[] =$this->ciphpmailer->phpmailer->ErrorInfo;
    		}	
    		
			
			}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
			
			}
		
		}
		
		
        $this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
		$this->smarty->view('about',$this->data);	
        
    }
	
	
		
}