<?php
class content_module {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
    	$this->CI->load->model('content_model');
    	$data["L"]=$this->CI->data["L"];
    	
    	$data["content"]=$this->CI->content_model->content_opt(array("c_id"=>$plugin["type_id"],"language_id"=>f_language_id()));
    	
    	if($data["content"]->num_rows){
    		$data["content"]=$data["content"]->row_array();
    	}else{
    	
    		$this->CI->db->where(array("plugin_id"=>$plugin["plugin_id"],"type_id"=>$plugin["type_id"]))->delete("plugin_to_page");
    	return "";
    	exit;
    	}
    	
    	
    	return $this->CI->smarty->fetch('plugins/content_module.tpl',$data);
    }
    
}