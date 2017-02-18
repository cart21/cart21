<?php
class social_link {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->data["uri_string"]=$this->CI->uri->uri_string;
		$this->data["param"]=unserialize($plugin["param"]);
		
		$this->data["social_links"]=$this->CI->quick_model->social_links();
		
    	
    	return $this->CI->smarty->fetch('plugins/social_link.tpl',$this->data);
    }
    
}