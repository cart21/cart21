<?php
class seo extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    function index() {

    	$this->data["dd"]="dd"; 
        
        
        $this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
       
     
        $this->smarty->view('meta/seo',$this->data);
    }

		
}