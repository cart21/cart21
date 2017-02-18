<?php
class latest_content {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$this->data["plugin"]=$plugin;
    	$this->CI->load->model('content_model');
    	
    	$this->data["L"]=$this->CI->data["L"];
    	$this->data["settings"]=$this->CI->data["settings"];
    	$this->data["param"]=unserialize($plugin["param"]);
    	
    	
    	$this->data["latest_contents"]=$this->CI->content_model->latest_content($this->data["param"]["default"]["limit"]);
    	
    	if(in_array($plugin["position"],array("left","right"))){
    			
    		$template_file='plugins/latest_content.tpl';
    	}else{
    			
    		$template_file='plugins/latest_content_center.tpl';
    	}
    	
    	return $this->CI->smarty->fetch($template_file,$this->data);
    }
    
}