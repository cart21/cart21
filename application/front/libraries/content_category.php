<?php
class content_category {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$this->data["plugin"]=$plugin;
    	
    	$this->CI->load->model("content_model");
    	$this->data["content_categories"]=$this->CI->content_model->category_tree();
  
    	$this->data["uri_string"]=$this->CI->uri->uri_string;
    	
    	$this->data["L"]=$this->CI->data["L"];
    	$this->data["POST"]=isset($this->CI->data["POST"])? $this->CI->data["POST"] : "";
    	

    	$this->data["POST"]["content_category_id"]=$this->CI->uri->rsegment(3);
    	
    	return $this->CI->smarty->fetch('plugins/content_category.tpl',$this->data);
    }
    
}