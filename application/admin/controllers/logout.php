<?php

	//$this->quick->dbg2();
     
     /*
     	$customer= $this->db->query('SELECT * FROM customer ');
    	$this->quick->dbg2($customer->num_rows());
        $this->quick->dbg2($customer->result());
        $this->quick->dbg2($customer->row());
        $this->quick->dbg2($customer->result_array() );
     */
     
     /*echo $this->config->site_url(); echo '<br>';
	echo $this->config->base_url(); echo '<br>';
	echo $this->config->system_url(); echo '<br>';
	*/
	
	//$this->quick->check_login();	
	
	//$this->quick->dbg($this->sessiondd->all_userdata('dd')); 
        //$this->sessiondd->destroy();
	//$this->quick->dbg2($this->sessiondd->all_userdata());

class logout extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
   
    function index() {
    	
    	$this->sessiondd->destroy();
        $this->smarty->assign("Name","ddd");
        $this->smarty->assign("title","Site | Register");
        
        redirect('/admin/login','refresh');
        
        
    }
   
    
   
     
		
}