<?php
class Account_Model extends CI_Model {

    var $last_id= '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
    function add_customer($data)
    {
    	
	$this->db->insert('customer', $data);
	return  $this->db->insert_id();
    }
    function add_address($data)
    {
    	$customer=$this->sessiondd->userdata('customer');
	//$this->quick->dbg($customer["customer_id"]);
	//$data["customer_id"]=$this->db->insert_id(); 
	$data["customer_id"]=$customer["customer_id"];
	
	$this->db->insert('address', $data);
    }
    function edit_address($data,$address_id)
    {
    	$customer=$this->sessiondd->userdata('customer');
    	
    	//$this->db->query("update address set firstname='".$data["firstname"]."'");
	
	$this->db->where('address_id', $address_id);
	$this->db->where('customer_id', $customer["customer_id"]);
	$this->db->update('address', $data); 

    }
    function get_Address() {
    	$customer=$this->sessiondd->userdata('customer');
	    
		$sql = "SELECT * FROM address as a
		
			LEFT JOIN city_list as cl
			ON cl.city_code=a.city_code
			
			LEFT JOIN country as c
			ON c.code=a.country_code and language_id=".f_language_id()."
			
		WHERE  a.customer_id=".$customer['customer_id']." "; 
	
        return $this->db->query($sql);
        	
    }
     function get_Addres($address_id)
    {
    	$customer=$this->sessiondd->userdata('customer');
		$customer["customer_id"];
    
	$sql = "SELECT * FROM address WHERE  customer_id=".$customer['customer_id']." AND address_id=".$address_id; 
        $addres=$this->db->query($sql);
        return $addres;
        	
    }  
    
    function get_Countries(){
    
    $this->db->where("language_id",f_language_id());
      return $this->db->order_by("sort_order","asc")->get("country");
    }
    
    function get_Cities($code)
    {
    	
	$sql = "SELECT * FROM city_list WHERE Country_Code='".$code."' "; 
        $Cities=$this->db->query($sql);
        return $Cities;
        	
    }
    
    function get_country($code)
    {
    	 
    	$sql = "SELECT * FROM country WHERE Code='".$code."' ";
    	
    	return $this->db->query($sql);
    	 
    }
    
    function get_city($code)
    {
    	 
    	$sql = "SELECT * FROM city_list WHERE city_code='".$code."' ";
    	
    	return $this->db->query($sql);
    	 
    }
    
    function login($email,$password )
    {
		$sql = "SELECT * FROM customer WHERE  email=$email AND password='$password' LIMIT 1"; 
      
        return $this->db->query($sql);
        	
    }
    
    
   	 	
   
 //$this->quick->dbg($data);   
 //echo $this->db->affected_rows();
 //echo  $this->db->insert_id();    

}