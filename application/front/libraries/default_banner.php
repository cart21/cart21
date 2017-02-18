<?php
class default_banner {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$data["plugin"]=$plugin;
    	$this->CI->load->model('banner_model');
    	
    	$data["banner"]=$this->CI->banner_model->banner($plugin["type_id"]);
    	$data["param"]=unserialize($plugin["param"]);
    	
    	if($data["banner"]->num_rows){
    		$data["banner"]=$data["banner"]->row_array();
    	}else{
    	
    		$this->CI->db->where(array("plugin_id"=>$plugin["plugin_id"],"type_id"=>$plugin["type_id"]))->delete("plugin_to_page");
    	return "";
    	exit;
    	}
    	
    	
    	return $this->CI->smarty->fetch('plugins/default_banner.tpl',$data);
    }
    
}