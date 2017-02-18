<?php
class tabs_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function products(){
    
    	$sql="select * from product as p
    	
    	left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=p.product_id  where p.status=1";
    		
    return $this->db->query($sql);
    }
    
    function tabs(){
    
    	$sql="select *, concat('maintab',main_tabs_id) as title from main_tabs order by sort_order asc";
    	
    return $this->db->query($sql);
    
    }
    
    function main_tabs($main_tabs_id,$language_id=null){
    	
    return $this->db->where("main_tabs_id",$main_tabs_id)->get("main_tabs");
    }
    
    function tabs_product($main_tabs_id,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
    
    	$sql="select *, p.title as title from main_tabs_to_product as mp
    	
    	left join main_tabs as mt
    	on mt.main_tabs_id=mp.main_tabs_id
    	
    	left join product as p 
    	on p.pl_id=mp.product_id  and p.language_id=".$language_id." and p.status=1
    	
    	left join  `product_image` as pi
    	on pi.product_id=mp.product_id and default_img=1
    	
    	 where p.language_id=".$language_id." and mp.main_tabs_id=".$main_tabs_id." order by mp.sort_order asc" ;
    		
    return $this->db->query($sql);
    
    }
    
    function tabs_product_id($main_tabs_id){
    
    	$this->db->where_in("main_tabs_id",$main_tabs_id);	
    return $this->db->get("main_tabs_to_product");
    
    }
    
    function product_tabs($product_id){
    
    	$this->db->where_in("product_id",$product_id);
    	return $this->db->get("main_tabs_to_product");
    
    }
    

    
}