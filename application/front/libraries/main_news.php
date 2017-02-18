<?php
class main_news {
 
    function __construct() {
    
    	$this->CI =& get_instance() ;
       
    }
 
    function index($plugin){
    	$this->CI->data["plugin"]=$plugin;
    	$this->CI->load->model('content_model');
    	
    	/// news ///
    	$this->CI->data["content_ids"]=$this->CI->content_model->content_ids($this->CI->data["settings"]["news"]);
    	
    	if($this->CI->data["content_ids"]->num_rows){
    	
    		$this->CI->db->order_by("content_id","desc")->where_in("c_id",array_column($this->CI->data["content_ids"]->result_array(),"content_id"));
    		$this->CI->data["news"]=$this->CI->content_model->contents();
    	
    	}
    	/// news ///
    	
    	return $this->CI->smarty->fetch('plugins/main_news.tpl',$this->CI->data);
    }
    
}