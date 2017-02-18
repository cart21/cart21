<?php
class product_filter {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index(){
    	$this->CI->data["uri_string"]=$this->CI->uri->uri_string;
    	$this->CI->data["category_nums"]= $this->CI->product_model->category_nums();
    	
    	$this->CI->data["product_categories"]=$this->CI->product_model->product_category_tree( array("no_left"=>1));
    	
    	return $this->CI->smarty->fetch('plugins/product_filter.tpl',$this->CI->data);
    }
    
}