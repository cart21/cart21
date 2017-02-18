<?php
class product_module {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	
    	$this->CI->load->model('product_model');
    	$this->data["settings"]=$this->CI->data["settings"];
    	$this->data["L"]=$this->CI->data["L"];
    	$this->data["product"]=$this->CI->product_model->product_opt(array("pl_id"=>$plugin["type_id"],"language_id"=>f_language_id()));
    	
    	if($this->data["product"]->num_rows){
    		$this->data["product"]=$this->data["product"]->row_array();
    		$this->data["product"]["images"]=$this->CI->product_model->product_image($plugin["type_id"])->row_array();
    	}else{
    	
    		$this->CI->db->where(array("plugin_id"=>$plugin["plugin_id"],"type_id"=>$plugin["type_id"]))->delete("plugin_to_page");
    	return "";
    	exit;
    	}
    	
    	$this->data["product_features_selected"]=$this->CI->product_model->get_product_feature_tree($plugin["type_id"],$this->data["product"]["language_id"],'1');
    	 
    	$this->data["product"]["price_d"]=$this->CI->product_model->product_price($plugin["type_id"]);
    
    	if(in_array($plugin["position"],array("left","right"))){
    			
    		$template_file='plugins/product_module_side.tpl';
    	}else{
    			
    		$template_file='plugins/product_module_center.tpl';
    	}
    	
    	return $this->CI->smarty->fetch($template_file,$this->data);
    }
    
}