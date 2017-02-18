<?php
class contact_info {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->data["uri_string"]=$this->CI->uri->uri_string;
		$this->data["param"]=unserialize($plugin["param"]);
		
		
		$this->data["languages"]=$this->CI->language_model->languages();
		
    	return $this->CI->smarty->fetch('plugins/contact_info.tpl',$this->data);
    }
    
}