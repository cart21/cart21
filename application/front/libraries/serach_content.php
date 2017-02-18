<?php
class serach_content {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
    	
		$this->data["product_s_link"]=$this->CI->quick_model->get_link("category");
		$this->data["content_s_link"]=$this->CI->quick_model->get_link("content_search");
		
    	return $this->CI->smarty->fetch('plugins/serach_content.tpl',$this->data);
    }
    
}