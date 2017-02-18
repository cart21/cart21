<?php
class main_page_slider  {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
        $this->CI->load->model('content_model');
       
    }
 
    function index($plugin){
    	$this->CI->load->model('main_page_model');
    	
    	$this->data["param"]=unserialize($plugin["param"]);
    	$this->data["L"]=$this->CI->data["L"];
    	$this->data["page_slides"]=$this->CI->main_page_model->page_slides();
    	
    	return $this->CI->smarty->fetch('plugins/main_page_slider.tpl',$this->data);
    }
    
}