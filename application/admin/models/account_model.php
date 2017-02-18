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
	//$data=$this->escapeAll($data);
	$this->db->insert('customer', $data);
    }
    function add_address($data)
    {
    	$customer=$this->sessiondd->userdata('customer');
	$data["customer_id"]=$customer["customer_id"];
	
	$this->db->insert('address', $data);
    }
    function login($email,$password )
    {
	$sql = "SELECT * FROM admin WHERE  email=$email AND password='$password' and status=1 LIMIT 1"; 
        	$customer=$this->db->query($sql);
        	return $customer;
        	
    }
    
    
   	 	
   
 //$this->quick->dbg($data);   
 //echo $this->db->affected_rows();
 //echo  $this->db->insert_id();    

}