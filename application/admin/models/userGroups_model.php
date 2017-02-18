<?php
class userGroups_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
    function get_group_section_permission($group_id)
    {


		$this->db->where(array("admin_group_id"=>$group_id));
	
	return $this->db->get('permission_admin_section')->result_array();
    }
    

 //$this->quick->dbg($data);   
 //echo $this->db->affected_rows();
 //echo  $this->db->insert_id();    

}