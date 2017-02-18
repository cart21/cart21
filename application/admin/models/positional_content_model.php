<?php
class positional_content_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }


	
	function pc_group($pc_id){
	
		$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			c.pc_id as pc_id,
    			c.positional_content_id as positional_content_id
	
    	FROM `language` as l
	
		left join positional_content as c
		on c.language_id=l.language_id and pc_id=".$pc_id."
	
		where l.status=1 ";
	
		return $this->db->query($sql);
	}
	
}