<?php


class elfinder extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    
    function index() {
    
   $this->permission->check_permission("view");
		
			
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
    $this->smarty->view('elfinder',$this->data);
 	}
 	 
		
}