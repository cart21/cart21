<?php
class main_tabs  {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
        $this->CI->load->model('content_model');
       
    }
 
    function index($plugin){
    	
    	
    	$this->CI->load->model('tabs_model');
    /// tabs ///
    	$this->CI->data["tabs"]=$this->CI->tabs_model->tabs()->result_array();
    	$this->CI->data["main_tabs_param"]=unserialize($plugin["param"]);
    	
    	$i=0;
    	foreach($this->CI->data["tabs"] as $id=>$tab ){
    		$f_mod=1;
    		foreach ($this->CI->tabs_model->tabs_product($tab["main_tabs_id"])->result_array() as $product){
    			
    			$this->CI->data["main_tabs"][$id]["product"][$i]=$product ;
    			$this->CI->data["main_tabs"][$id]["product"][$i]["product_features_selected"]=$this->CI->product_model->get_product_feature_tree($product["pl_id"],f_language_id(),'1');
    			
    			$this->CI->data["main_tabs"][$id]["product"][$i]["num"]=$f_mod;
    			
    			if(fmod($f_mod,$this->CI->data["main_tabs_param"]["settings"]["column"])==0){
    				$f_mod=0;
    			}
    			
    			$i++;
    			$f_mod++;
    			
    		}

    	}
    	/// tabs ///
    	
    	return $this->CI->smarty->fetch('plugins/main_tabs.tpl',$this->CI->data);
    }
    
}