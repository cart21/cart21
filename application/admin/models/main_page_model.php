<?php
class main_page_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function page_slides($language_id=null){
    	
    	if(! $language_id){
    		$language_id=language_id();
    	}
    	
    	$this->db->where(array("language_id"=>$language_id));
    
    return $this->db->order_by("sort_order","asc")->get("page_slides");
    }
   
    function mainpage($language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
    
    	$result=$this->db->where(array("language_id"=>$language_id))->get("main_page_option");
    	
    	if($result->num_rows==0){
    		$result=$this->db->get("main_page_option");
    	
    	}
    	return $result;
    }
}