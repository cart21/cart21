<?php
class product_category {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	

    	$this->CI->data["uri_string"]=$this->CI->uri->uri_string;
    	
    	$this->CI->data["category_plugin"]=$plugin;
    	
    	$this->CI->load->model("product_model");
    	
    	//$this->CI->data["category_nums"]= $this->CI->product_model->category_nums();
    	
    	$this->CI->data["product_categories"]=$this->CI->product_model->product_category_tree( array("no_left"=>1));
    	
    	return $this->CI->smarty->fetch('plugins/product_category.tpl',$this->CI->data);
    }
    
}