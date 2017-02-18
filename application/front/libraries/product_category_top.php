<?php
class product_category_top {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index(){
    	
    	$this->CI->load->model("product_model");
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->data["uri_string"]=$this->CI->uri->uri_string;
    	
    	$this->data["category_nums"]= $this->CI->product_model->category_nums();
    	
    	$this->data["product_categories"]=$this->CI->product_model->product_category_tree( array("no_top"=>1));
    	
    	return $this->CI->smarty->fetch('plugins/product_category_top.tpl',$this->data);
    }
    
}