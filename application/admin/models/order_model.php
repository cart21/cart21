<?php
class order_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function order($order_id){
    
    	$sql="select * ,o.date_added as date_added from `order` as o
    	
    			left join customer as c
    			on c.customer_id=o.customer_id
    		
    		where o.order_id=".$order_id." ";
    		
    return $this->db->query($sql);
    }
    
    function order_products($order_id){
    
    	$sql="select *,op.number as number from order_product as op
    			left join product as p
    			on p.pl_id=op.product_id and language_id=".language_id()."
    			
    			left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=op.product_id
    		
    		where op.order_id=".$order_id." ";
    		
    return $this->db->query($sql);
    }
    
    
    
    function order_status_all(){
    	
    	return $this->db->order_by("sort_order","asc")->get("order_status");
    }
    
    function order_status($id){
    	
    	return $this->db->where(array("order_status_id"=>$id))->get("order_status");
    }

    
    
    function order_shipping_status_all(){
    
    	return $this->db->get("order_shipping_status");
    }
    
    function order_shipping_status($id){
    	 
    	 return $this->db->where("status_id",$id)->get("order_shipping_status");
    }
    
    
    function banks(){
    	 
    	return $this->db->get("bank");
    }
    
    function bank($id){
    
    	return $this->db->where("bank_id",$id)->get("bank");
    }
    
    
    function shipping_companies(){
    
    	return $this->db->get("shipping_company");
    }

    function shipping_company($id){
    
    	return $this->db->where("company_id",$id)->get("shipping_company");
    }
    
    function taxes(){
    
    	return $this->db->get("tax");
    }
    
    function tax($id){
    
    	return $this->db->where("tax_id",$id)->get("tax");
    }
    
    function continents(){
    	return $this->db->get("continents");
    }
    
	function get_Countries(){
    
     	$this->db->where("language_id",language_id());
    	return $this->db->order_by("sort_order","asc")->get("country");
    }
    
    function country_group($cl_id){
    	 
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			p.cl_id as cl_id,
    			p.country_id as country_id
    
    	FROM `language` as l
   
		left join country as p
		on p.language_id=l.language_id and cl_id=".$cl_id."
   
		 where l.status=1 ";
    
    	return $this->db->query($sql);
    }
   
}