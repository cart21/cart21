<?php
class main_page {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
        $this->CI->load->model('content_model');
       
    }
 
    function index(){
    	
    	$this->CI->load->model('main_page_model');
    	
    	$main_page_option=$this->CI->main_page_model->mainpage()->row_array();
    	 
    	$this->CI->data["POST"]["content"]=$main_page_option["content"];
    	
    	return $this->CI->smarty->fetch('plugins/main_page.tpl',$this->CI->data);
    }
    
}