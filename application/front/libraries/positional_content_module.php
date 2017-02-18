<?php
class positional_content_module {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
    	$data["L"]=$this->CI->data["L"];
    	
    	$data["content"]=$this->CI->db->where(array("pc_id"=>$plugin["type_id"],"status"=>1,"language_id"=>f_language_id()))->get("positional_content");
    	
    	if($data["content"]->num_rows){
    		$data["content"]=$data["content"]->row_array();
    	}else{
    	
    		//$this->CI->db->where(array("plugin_id"=>$plugin["plugin_id"],"type_id"=>$plugin["type_id"]))->delete("plugin_to_page");
    	return "";
    	exit;
    	}
    	
    	
    	return $this->CI->smarty->fetch('plugins/positional_content_module.tpl',$data);
    }
    
}