<?php
class category_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function product_category($product_category_id){
    
    return $this->db->where_in("product_category_id",$product_category_id)->get("product_category");
    }
    
    function product_categories(){
    
    	return $this->db->get("content_category");
    }
   
}