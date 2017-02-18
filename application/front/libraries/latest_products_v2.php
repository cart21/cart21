<?php
class latest_products_v2 {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$this->data["plugin"]=$plugin;
    	$this->CI->load->model('product_model');
    	
    	$this->data["L"]=$this->CI->data["L"];
    	$this->data["settings"]=$this->CI->data["settings"];
    	$this->data["param"]=unserialize($plugin["param"]);
    	
    	/// plugin latest product on the left ///
		$this->data["latest_products"]=$this->CI->product_model->latest_product($this->data["param"]["sample"]["limit"])->result_array();
		/// plugin latest product on the left ///
		
    	
		
		if(in_array($plugin["position"],array("left","right"))){
			
			$template_file='plugins/latest_products_v2.tpl';
		}else{
			
			$template_file='plugins/latest_products_center_v2.tpl';
		}
		
    	return $this->CI->smarty->fetch($template_file,$this->data);
    }
    
}