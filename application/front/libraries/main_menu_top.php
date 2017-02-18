<?php
class main_menu_top {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
   		$this->CI->load->model("meta_model");
   		$this->data["settings"]=$this->CI->data["settings"];
   	
		$this->data["main_menu_category"]=$this->CI->meta_model->main_menu_tree();
		$this->data["uri_string"]=$this->CI->uri->uri_string;
		
		$this->data["L"]=$this->CI->data["L"];
		
    	return $this->CI->smarty->fetch('plugins/main_menu_top.tpl',$this->data);
    }
    
}