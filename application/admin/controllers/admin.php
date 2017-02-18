<?php
class admin extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
    }
 
    function index() {

    	$this->smarty->assign("Header",$this->quick->Header( $this->smarty));
        $this->smarty->assign("Footer",$this->quick->Footer( $this->smarty));
        
       
     
       if(!$this->quick->logged_in()) {
		
		redirect('admin/login');
		}
        $this->smarty->view('index');
    }
    function login() {
    
        $this->smarty->assign("Header",$this->quick->Header( $this->smarty));
        $this->smarty->assign("Footer",$this->quick->Footer( $this->smarty));

       
     	$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_email_exist');
      	
      	if ( $this->form_validation->run()== FALSE ){
      	
      	} 
      	
      
        if(  $this->input->post() && !$this->quick->logged_in()&& $this->form_validation->run() ){
        	$this->load->model('Account_Model');
        	
        	$email=$this->db->escape($this->input->post("email"));
        	$password=sha1($this->input->post("password"));
        	$this->Account_Model->login($email,$password );
		
	}
	
	if($this->quick->logged_in()) {
		$this->load->helper('url');
		redirect('admin');
	}
	
	
        $this->smarty->view('account/login');
 
    }
    function email_exist($email){
    	
    	$this->db->where("email",$email);
    	$customer=$this->db->get("customer");
    
    	
    	if($customer->num_rows>0){
    	return true;
    	}else{
    	 $this->smarty->assign("error",$this->input->post("email")."this email is not registered ");
    	return false;
    	}
    
    }
    
    function logout() {
    	
    	$this->sessiondd->destroy();
        
        
        redirect('/admin/login','refresh');
        
        
    }
   
    function escapeAll($data)
	   	{
	    	foreach($data as $key => $value){
	    	$data[$key]=$this->db->escape($value);
	    	
	    	}
    	return $data;
    	}
   


		
}