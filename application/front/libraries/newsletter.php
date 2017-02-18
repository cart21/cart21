<?php
class newsletter {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$this->data["plugin"]=$plugin;
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->data["param"]=unserialize($plugin["param"]);

		
    	return $this->CI->smarty->fetch('plugins/newsletter.tpl',$this->data);
    }
    
}