<?php
class under_construction extends CI_Controller {
 
    function __construct() {
        parent::__construct();
    }
 
    function index() {

	
       	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
	$this->smarty->view('under_construction',$this->data);
	}
    
   
}