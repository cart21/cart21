<?php
class link_top {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->data["uri_string"]=$this->CI->uri->uri_string;
		$this->data["param"]=unserialize($plugin["param"]);
		
		$this->data["links"]=$this->CI->quick_model->top_link();
		$this->data["languages"]=$this->CI->language_model->languages();
		$this->data["static_pages"]=array_column($this->CI->language_model->language_page()->result_array(),"link","class_routes");
    	
    	return $this->CI->smarty->fetch('plugins/link_top.tpl',$this->data);
    }
    
}