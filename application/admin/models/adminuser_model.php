<?php
class adminuser_model extends CI_Model {

    var $last_id= '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
    
    function getUsers($page ,$per_page)
    {
    	$Users=$this->db->query("SELECT * FROM admin ORDER BY customer_id ASC limit ".$page*$per_page." , ".$per_page);
	
	return $Users->result_array();
    }
    
    function getTotalUsers()
    {
		$Users=$this->db->query("SELECT * FROM admin");
	
	return $Users->num_rows();
    }
    
    function getUser($ids)
    {
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		$Users=$this->db->query("SELECT * FROM admin AS a
		
		LEFT JOIN admin_to_group AS atg
		ON atg.admin_id=a.customer_id
		
		LEFT JOIN admin_group AS ag
		ON ag.admin_group_id=atg.admin_group_id
		
		WHERE a.customer_id IN (".$ids.")  ");
		
		return $Users->result_array();
    }
    
    function deleteUsers($ids)
    {
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
		
		$Users=$this->db->query("DELETE FROM admin WHERE customer_id IN (".$ids.") ");
		
    }
    
    function editUsers($ids)
    {
		if( is_array($ids) )
		{
		$ids=implode(',',$ids);		
		}
	
    }
    
   
    function login($email,$password )
    {
	$sql = "SELECT * FROM admin WHERE  email=$email AND password='$password' LIMIT 1"; 
        	$customer=$this->db->query($sql);
        	
        	if($customer->num_rows()>0){
        	
        		$pattern=array(
        		"customer_id" =>"",
        		"firstname" =>"",
        		"lastname" =>"",
        		"email" =>""
        		);
        		$sesion=array_intersect_key($customer->row_array(),$pattern);
        		//var_dump($sesion);
			$this->sessiondd->set_userdata('admin',$sesion);
			
			
		}
    }
    
    function get_admin_group($admin_id){
    
		$sql="SELECT * FROM `admin_group` as ag
	
		LEFT JOIN admin_to_group AS atg
		ON atg.`admin_group_id`=ag.`admin_group_id`
		
		WHERE  atg.`admin_id`=$admin_id";
		
	return $this->db->query($sql);
    }

    function gl_group($gl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			p.gl_id as gl_id,
    			p.admin_group_id as admin_group_id
    
    	FROM `language` as l
    
		left join admin_group as p
		on p.language_id=l.language_id and gl_id=".$gl_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }    
    
    function cl_group($cl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			p.cl_id as cl_id,
    			p.customer_group_id as customer_group_id
    
    	FROM `language` as l
    
		left join customer_group as p
		on p.language_id=l.language_id and cl_id=".$cl_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    
    function admin_groups(){
    	
    	return $this->db->where("language_id",language_id())->get("admin_group");
    	
    }
    

    function customer_groups(){
    	 
    	return $this->db->where("language_id",language_id())->get("customer_group");
    	 
    }
 
}