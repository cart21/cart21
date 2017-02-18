<?php
class today_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
         
    }

    function orders(){
    	 
    	return $this->db->query("SELECT * FROM `order`  where  FROM_UNIXTIME(`date_added`,'%Y-%m-%d') = curdate()");
    }
    
    function comments(){
    	
    	return $this->db->query("SELECT * FROM `product_comment`  where  FROM_UNIXTIME(`date_added`,'%Y-%m-%d') = curdate()");
    }
    function tickets(){
    	
    	return $this->db->query("SELECT * FROM `support`  where  FROM_UNIXTIME(`date_added`,'%Y-%m-%d') = curdate()");
    }
    function customers(){
    	
    	return $this->db->query("SELECT * FROM `customer`  where  FROM_UNIXTIME(`date_added`,'%Y-%m-%d') = curdate()");
    }
   
}