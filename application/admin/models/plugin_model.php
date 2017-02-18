<?php
class plugin_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
	function plugin($plugin_id){
		
		return $this->db->where("plugin_id",$plugin_id)->get("plugin");
	}
	
	function plugin_by_key($p_key){
	
		return $this->db->where("p_key",$p_key)->get("plugin");
	}
	
	function delete_plugin($plugin_id){
	
		$this->db->where("plugin_id",$plugin_id)->delete("plugin");
		$this->db->where("plugin_id",$plugin_id)->delete("plugin_to_page");
	}
	function delete_plugin_to_page($data){
		

		$this->db->where($data)->delete("plugin_to_page");
	}
	
	function plugin_types(){
		
	return	$this->db->get("plugin_type")->result_array();
	}

	function plugin_types_arr(){
	
		$types=$this->plugin_types();
		return	array_column($types,"title","plugin_type_id");
	}
	
	function page_positions(){
	
		return	$this->db->get("page_position");
	}
	
	function assigned_positions($plugin_id,$type_id=null){
		
		if(!$type_id){
			$type_id=0;
		}
		
		$this->db->where(array("type_id"=>$type_id,"plugin_id"=>$plugin_id));
		
		return $this->db->select("meta_id,plugin_id,page_position_id,type_id")->get("plugin_to_page");
	}
	 
	function page_plugin($page_id){
	
		$sql="SELECT *,po.title as position,p.title as title  FROM `plugin_to_page` as pp
	
		left join plugin as p
		on p.plugin_id=pp.plugin_id
	
		left join page_position as po
		on po.page_position_id=pp.page_position_id
	
		where p.status=1 and pp.meta_id=".$page_id." order by pp.sort_order asc";
		return $this->db->query($sql);
	}
}