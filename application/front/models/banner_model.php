<?php
class banner_model extends CI_Model {

	function __construct(){

        parent::__construct();
    }
    
    function banner($banner_id,$language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
     	$this->db->where("language_id",$language_id);
    
    return $this->db->where("bl_id",$banner_id)->get("banner");
    }
    
}