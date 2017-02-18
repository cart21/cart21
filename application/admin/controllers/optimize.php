<?php
class admin extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    function index() {

    	/// delete meta link that does not exist
    	
    	/// delete files that support record not have 
    		
    }
    
    function dedect_probe() {
    
    	
    }
    
    
    function create_() {
    
    	 
    	/// create link that product doesnt have
    	
    }
    function no_relation(){
    	
    	// customer_to_group
    	
    	// product_to_meta_delete
    	$this->db->query("delete from meta WHERE type=4 and `type_id` not in( select product_id from product )");
    	// product_to_meta_delete
    	
    	// product_to_image
    	$image= $this->db->query("select * from  `product_image` WHERE `product_id` not in( select pl_id from product )");
    	
    	if($image->num_rows){
    		
    		foreach ($image->result_array() as $row ){
    			
    			unlink($_SERVER["DOCUMENT_ROOT"].'/'.$row["image_loc"]);
    			$this->db->where("product_image_id",$row["product_image_id"])->delete("product_image");
    		}
    	}
    	
    	// product_to_image
    	
    	// product_to_category
    	
    	$this->db->query("delete from  `product_to_category` WHERE `product_id` not in( select pl_id from product )");
    	
    	// tabs_to_product
    	$this->db->query("delete from main_tabs_to_product where product_id not in ( select pl_id from product )");
    	
    	
    	// delete meta that does not exist
    	
    	//language_c_to_page
    	
    }
    

   function update1(){}
		
}