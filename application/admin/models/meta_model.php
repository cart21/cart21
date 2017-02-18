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
    	
    	$this->update_m_id($data);
    
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
	 			$data["meta_id"]=$this->db->insert_id();
	 			$this->update_m_id($data);
	 			return $data;
	 		}else{
	 			echo "Error: meta data not affected" ;exit;
	 		}
	 	}
    
    }
    
    function update_m_id($meta){
    	
    	$sql="SELECT * FROM `meta` WHERE  `type`=".$meta["type"]." and `type_l_id`=".$meta["type_l_id"]." and `m_id` is not null";
    	$check=$this->db->query($sql);
    	
    	if($check->num_rows){
    		
    		$this->db->where("meta_id",$meta["meta_id"])->update("meta",array("m_id"=>$check->row()->m_id));
    	}else{
    		
    		$this->db->where(array("type"=>$meta["type"],"type_l_id"=>$meta["type_l_id"]))->update("meta",array("m_id"=>$meta["meta_id"]));
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
    
    function static_pages($language_id=null){
    
    	if(! $language_id){
    		$language_id=language_id();
    	}
    
    	$this->db->where("language_id",$language_id);
    	return $this->db->where("type",1)->order_by("title","asc")->get("meta");
    }
    
    function main_menu($main_category_id=0){
    	 
    	$sql="select * from link_to_menu as lm

    			left join meta as m
    			on m.m_id = lm.sub_m_id 
    			
    			where lm.top_m_id=".$main_category_id." and  m.language_id=".language_id()."  order by m.main_menu_sort asc
    					
    			";
    	
    	return $this->db->query($sql);
    }
    
    function main_menu_tree($main_category_id=0){
    	$M=$this->main_menu($main_category_id);
    	if($M->num_rows){
    		foreach ($M->result_array() as $k=>$mc){
    
    			$shape["info"]=$mc;
    			$shape["sub"]=$this->main_menu_tree($mc["sub_m_id"]);
    			$catgories[]=$shape;
    		}
    		return $catgories;
    	}else{
    		return null;
    	}
    }
   
    function lucky_main_menu($id){
    	
    	return $this->db->where("sub_m_id",$id)->get("link_to_menu");
    }

    function meta_language_create($M){
    
    	$sql="SELECT l.language_id,l.short_name FROM `language` as l
    
			left join meta as m
			on m.language_id=l.language_id and m.m_id=".$M["m_id"]."
    
			where l.status=1 and (m.meta_id is null)";
    
    	$result=$this->db->query($sql);
    
    	if($result->num_rows){
    
    		$intersect_key=array(
    				"m_id"=>1,
    				"class_routes"=>1,
    				"type"=>1,
    				"type_id"=>1,
    				"footer"=>1,
    				"foot_sort"=>1,
    				"top_menu"=>1,
    				"top_sort"=>1,
    				"no_delete"=>1,
    				"title"=>1,
    				"link"=>1
    		);
    		$new_data=array_intersect_key($M,$intersect_key);
    
    		foreach($result->result_array() as $l){
  
    			$new_data["language_id"]=$l["language_id"];
    			
    			$new_data["link"]= $M["type"]==9 ? $M["link"] : $l["short_name"]."-".$M["link"];
    			$this->db->insert("meta",$new_data);
    		}
    
    	}
    }
    
    function create_new_meta_static(){
    	
    	foreach ($this->static_pages()->result_array() as $static_page){
    		
    		$this->meta_language_create($static_page);
    	}
    	
    }
    
}