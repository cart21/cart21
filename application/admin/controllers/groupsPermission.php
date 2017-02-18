<?php
class groupsPermission extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    
    function index() {
    
		$this->permission->check_permission("view");
	
		$this->db->select('*');
		$this->db->from('admin_group');
		$this->db->join(
		'permission_admin_section', 
		"permission_admin_section.admin_group_id=admin_group.admin_group_id",
		'LEFT');
	
		$this->db->join(
		'permission_sections', 
		"permission_sections.section_id=permission_admin_section.section_id",
		'LEFT');
	

		$result_permission=$this->db->order_by("permission_sections.name asc")->get();
	
		$this->data["result_permission_key"]=array_keys($result_permission->row_array());	
		$this->data["result_permission"]=$result_permission->result_array();
	
			$this->quick->Header("");
			$this->quick->Top_menu("");
			$this->quick->Footer("");  
		$this->smarty->view('groupsPermission',$this->data);
 
    }

     
		
}