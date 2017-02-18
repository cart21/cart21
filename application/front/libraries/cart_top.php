<?php
class cart_top {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index(){
    	
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		$this->CI->load->model("product_model");
		
		$cart_product=$this->CI->product_model->cart_products();
		$this->data["products"]=$cart_product["products"];
		$this->data["cart_summary"]=$cart_product["cart_summary"];
		 
    	
    	return $this->CI->smarty->fetch('plugins/cart_top.tpl',$this->data);
    }
    
}