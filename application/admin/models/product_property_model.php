<?php
class product_property_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function product_property($product_property_id){
    
    return $this->db->where_in("product_property_id",$product_property_id)->get("product_property");
    }
    
    function properties($language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
    	return $this->db->where(array("language_id"=>$language_id,"status"=>1))->get("product_property");
    }
    
    function product_property_ids($product_id){
    
    	return  $this->db->where("product_id",$product_id)->get("product_to_property");
    }
    function delete_product_property($product_id){
    
    	$this->db->where("product_id",$product_id)->delete("product_to_property");
    
    }
    

   
}