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
	
	function function_list($plugin_id){
		
		$sql="select *, m.title as page_title, pl.title as plugin_title from plugin_to_page as ptp
				
			left join meta as m 
			on m.m_id=ptp.meta_id and language_id =".$this->quick->language_id()." 

			left join plugin as pl
			on pl.plugin_id=ptp.plugin_id
					 
			where ptp.plugin_id=".$plugin_id;
		
		return $this->db->query($sql);
	}
	
	function page_function_list($meta_id){
	
		$sql="select *, m.title as page_title, pl.title as plugin_title from plugin_to_page as ptp
	
			left join meta as m
			on m.m_id=ptp.meta_id and language_id =".$this->quick->language_id()."
	
			left join plugin as pl
			on pl.plugin_id=ptp.plugin_id
	
			where ptp.meta_id=".$meta_id." and pl.status=1";
	
		return $this->db->query($sql);
	}
	
	function plugin_key_staus($p_key){
		$plugin= $this->db->where(array("p_key"=>$p_key,"status"=>1))->get("plugin");
		 
		 if($plugin->num_rows){
		 	return true;
		 }else{
		 	
		 	return false;
		 }
	}
	
	function page_plugin($page_id){
	
	$sql="SELECT *,po.title as position  FROM `plugin_to_page` as pp
	
		left join plugin as p
		on p.plugin_id=pp.plugin_id
	
		left join page_position as po
		on po.page_position_id=pp.page_position_id
	
		where p.status=1 and pp.meta_id=".$page_id." order by pp.sort_order asc";
	return $this->db->query($sql);
	}
   
}