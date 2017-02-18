<?php
class main_page_brands {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
        $this->CI->load->model('content_model');
       
    }
 
    function index(){
    	$this->CI->data["brands"]=$this->CI->product_model->brands_own();
    	
    	return $this->CI->smarty->fetch('plugins/main_page_brands.tpl',$this->CI->data);
    }
    
}