<?php
class User extends CI_Model {

    var $last_id= '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
       
        parent::__construct();
    }
    
   
    
    function getUsers($page ,$per_page){
    	
		$Users=$this->db->query("SELECT * FROM customer ORDER BY customer_id ASC limit ".$page*$per_page." , ".$per_page);
	
	return $Users->result_array();
    }
    
    function getTotalUsers(){
	
		$Users=$this->db->query("SELECT * FROM customer");
	
	return $Users->num_rows();
    }
    
    function getUser($ids){
    
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		$Users=$this->db->query("SELECT * FROM customer AS c
		
		LEFT JOIN customer_to_group AS ctg
		ON ctg.customer_id=c.customer_id
		
		LEFT JOIN customer_group AS cg
		ON cg.customer_group_id=ctg.customer_group_id
		
		WHERE c.customer_id IN (".$ids.")  ");
		
		return $Users->result_array();
    }
    
    function customer($ids){
    
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		$customer=$this->db->query("SELECT * FROM customer AS c
		
		LEFT JOIN customer_to_group AS ctg
		ON ctg.customer_id=c.customer_id
		
		LEFT JOIN customer_group AS cg
		ON cg.customer_group_id=ctg.customer_group_id
		
		WHERE c.customer_id IN (".$ids.")  ");
		
		return $customer;
    }
    
    function customer2($ids){
    
		$customer=$this->db->where("customer_id",$ids)->get("customer");
		
	return $customer;
    }
    
    function deleteUsers($ids){
    
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		$Users=$this->db->query("DELETE FROM customer WHERE customer_id IN (".$ids.") ");
		
		
    }
    
    function editUsers($ids){
    
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		
    }
    
    function add_address($data){
    
		$customer=$this->sessiondd->userdata('customer');
	
		$data["customer_id"]=$customer["customer_id"];
		
		$this->db->insert('address', $data);
    }
    
    function login($email,$password ){
    
		$sql = "SELECT * FROM admin WHERE  email=$email AND password='$password' LIMIT 1"; 
        	$customer=$this->db->query($sql);
        	
        	if($customer->num_rows()>0){
        	
        		$pattern=array(
        		"customer_id" =>"",
        		"firstname" =>"",
        		"lastname" =>"",
        		"email" =>"",
        		"image_url" =>""
        		);
        		$sesion=array_intersect_key($customer->row_array(),$pattern);
        		
			$this->sessiondd->set_userdata('admin',$sesion);
			
			
		}
    }
    
     
}