<?php
class main_menu_left {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
    	$this->data["plugin"]=$plugin;
   		$this->CI->load->model("meta_model");
		$this->data["main_menu_categories"]=$this->CI->meta_model->main_menu_tree();
		$this->data["uri_string"]=$this->CI->uri->uri_string;
		
		$this->data["L"]=$this->CI->data["L"];
		
    	return $this->CI->smarty->fetch('plugins/main_menu_left.tpl',$this->data);
    }
    
}