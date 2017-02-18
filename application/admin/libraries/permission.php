<?php
class permission {

	var $CI;
	var $admin_id;
	var $admin_group_id;
	var $permission_type;
	
	var $response;

	function __construct(){
		
		 $this->CI =& get_instance() ;
		 
		// if(!$this->CI->quick->logged_in()) { return false;}
		 
		 $admin_data			=$this->CI->sessiondd->userdata('admin');
		 $this->admin_id		=$admin_data["customer_id"];
		 //exit;
		 $this->permission_section	= $this->CI->uri->rsegments[1];
		 //dbg( $this->CI->uri);
		 $this->set_admin_group();

	}
	
	
	function set_admin_group(){
	
		$this->CI->db->where("admin_id",$this->admin_id);
    	$admin_group=$this->CI->db->get("admin_to_group");
    	
    	$this->admin_group_id=$this->CI->quick->array_column($admin_group->result_array(),"admin_group_id");//$group_ids;
    	
	}
	
	
	/*
	* check_permission("userall","view" )
	*/
	function check_permission($permission="view" ,$permission_section=""){
		
		$this->CI->quick->onlyLoginUser();
		
		if(in_array('1',$this->admin_group_id)){ return TRUE;}
		
		$this->CI->db->select('*');
		$this->CI->db->from('admin_group');
		$this->CI->db->join(
		'permission_admin_section', 
		"permission_admin_section.admin_group_id=admin_group.admin_group_id",
		'LEFT');
		
		$this->CI->db->join(
		'permission_sections', 
		"permission_sections.section_id=permission_admin_section.section_id",
		'LEFT');
		
		$data=array(
			"LOWER(permission_sections.class)"=> strtolower($this->permission_section),//$permission_section,
			"permission_admin_section.permission"=>$permission
		);
		
			$this->CI->db->where($data);
			$this->CI->db->where_in("permission_admin_section.admin_group_id",$this->admin_group_id);
			
			$result=$this->CI->db->get();
			
		  //$this->dbg2($result->result_array());
			
			if($result->num_rows==0){
			
				//echo "you dont have ".$permission." permission on this section";
				$this->CI->quick->errors[]="you dont have  ".strtoupper($permission)." permission on this section";
				
				if(in_array($permission,array("view"))){
			
					$data["Header"]=$this->CI->quick->Header( $this->CI->smarty);
					$data["Footer"]=$this->CI->quick->Footer( $this->CI->smarty);
					$this->CI->smarty->view('error',$data);
					exit;
				
				}
		   	
				return false;
			}else{
				return true;
			}
		
	}
	
	


}