<?php
class meta_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function meta_types(){
    
    
    return $this->db->order_by("sort_order","asc")->get("meta_type");
    }
    
    function meta_type($data){
    
    return $this->db->where($data)->get("meta_type");
    }
    
    function update_meta($data){
 
    	$this->db->where("type_id <>".$data["type_id"]);
    	$this->db->where(array("link"=>$data["link"]));
    	$meta_check=$this->db->get("meta");
    	
    	if($meta_check->num_rows>0){
    		$data["link"]=str_replace(".html","-".$data["type"].".html",$data["link"]);
    		return	$this->update_meta($data);
    	}else{
    		$this->db->where(array("type"=>$data["type"],"type_id"=>$data["type_id"]))->update("meta",$data);
    		return $data;
    	}
    
    }
    
    function meta($data){
    
    return $this->db->where($data)->get("meta");
    }
    
    function insert_meta($data,$text="2"){
    
    	$meta_check=$this->meta(array("link"=>$data["link"]));
    	
    	if($meta_check->num_rows>0){
    		
    		if($text=="2"){
    			$text=$data["type_id"];
    		}
    		
	 		$data["link"]=str_replace(".html",$text.".html",$data["link"]);
	 	return	$this->insert_meta($data);
	 	}else{
	 		$this->db->insert("meta",$data);

	 		if($this->db->affected_rows()){
	 			return $data;
	 		}else{
	 			echo "Error: meta data not affected" ;exit;
	 		}
	 	}
    
    }
    
    function m_group($m_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			m.m_id as m_id,
    			m.meta_id as meta_id
    
    	FROM `language` as l
    
		left join meta as m
		on m.language_id=l.language_id and m_id=".$m_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    function main_menu($main_category_id=0){
    
    	$sql="select * from link_to_menu as lm
    
    			left join meta as m
    			on m.m_id = lm.sub_m_id
    
    			where lm.top_m_id=".$main_category_id." and  m.language_id=".f_language_id()."  order by m.main_menu_sort asc
    	
    			";
    	 
    	return $this->db->query($sql);
    }
    
    
    function main_menu_tree($main_category_id=0){
    
    	if($this->main_menu($main_category_id)->num_rows){
    		foreach ($this->main_menu($main_category_id)->result_array() as $k=>$mc){
    
    			$shape["info"]=$mc;
    			$shape["sub"]=$this->main_menu_tree($mc["sub_m_id"]);
    			$catgories[]=$shape;
    		}
    		return $catgories;
    	}else{
    		return null;
    	}
    }
    
   

}