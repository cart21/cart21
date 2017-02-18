<?php
class product_brand_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function product_brand($product_brand_id){
    
    return $this->db->where_in("product_brand_id",$product_brand_id)->get("product_brand");
    }
    
    function brands($language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
    	
    	return $this->db->where(array("language_id"=>$language_id,"status"=>1))->get("product_brand");
    }
    
    function product_brand_ids($product_id){
    
    	return  $this->db->where("product_id",$product_id)->get("product_to_brand");
    }
    function delete_product_brand($product_id){
    
    	$this->db->where("product_id",$product_id)->delete("product_to_brand");
    
    }
    

   
}