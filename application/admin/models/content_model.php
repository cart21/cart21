<?php
class content_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function contents(){
    
    
    return $this->db->get("content");
    }
    
    function content($content_id){
    
    
    return $this->db->where("content_id",$content_id)->get("content");
    }
     
    function content_categories($language_id=null){
    
     	if(! $language_id){
     		$language_id=$_SESSION["cart21_a_language"]["language_id"];
     	}
     	$this->db->where("language_id",$language_id);
    	
    	$this->db->order_by("sort_order","asc");
    return $this->db->get("content_category");
    }
    
    function content_categories_left($language_id=null){
    
     	if(! $language_id){
     		$language_id=$_SESSION["cart21_a_language"]["language_id"];
     	}
     	$this->db->where("content_category.language_id",$language_id);
    	
    	$this->db->select("*,content_type.title as type_title,content_category.title as title")->from("content_category");
    	$this->db->join("content_type","content_category.content_type_id=content_type.content_type_id","left");
    	
    	$this->db->order_by("content_type.sort_order","asc");
    	$this->db->order_by("content_category.sort_order","asc");
    return $this->db->get();
    }
    
    function content_types($language_id=null){
    
     	if(! $language_id){
     		$language_id=$_SESSION["cart21_a_language"]["language_id"];
     	}
     	$this->db->where("language_id",$language_id);
    
     return $this->db->order_by("sort_order","asc")->get("content_type");
    }
    
    function content_category($category_id){
    
     return $this->db->where("content_category_id",$category_id)->get("content_category");
    }
    
    function content_type($content_type_id){
    
     return $this->db->where("content_type_id",$content_type_id)->get("content_type");
    }
    
    function delete_content_category($content_id){
    
    $this->db->where_in("content_id",$content_id)->delete("content_to_category");
    
    }

	function content_category_ids($content_id){
	
	return  $this->db->where("content_id",$content_id)->get("content_to_category");
	}
	
	function content_id_by_category_ids($category_id){
	
	//return  $this->db->where_in("content_category_id",$category_id)->get("content_to_category");
	return  $this->db->query("select * from content_to_category where content_category_id  in (".implode(",",$category_id).")");
	}

	function ct_group($ct_id){
	
		$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			ct.ct_id as ct_id,
    			ct.content_type_id as content_type_id
	
    	FROM `language` as l
	
		left join content_type as ct
		on ct.language_id=l.language_id and ct_id=".$ct_id."
	
		where l.status=1 ";
	
		return $this->db->query($sql);
	}
	
	function cc_group($cc_id){
	
		$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			cc.cc_id as cc_id,
    			cc.content_category_id as content_category_id
	
    	FROM `language` as l
	
		left join content_category as cc
		on cc.language_id=l.language_id and cc_id=".$cc_id."
	
		where l.status=1 ";
	
		return $this->db->query($sql);
	}
	
	function c_group($c_id){
	
		$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			c.c_id as c_id,
    			c.content_id as content_id
	
    	FROM `language` as l
	
		left join content as c
		on c.language_id=l.language_id and c_id=".$c_id."
	
		where l.status=1 ";
	
		return $this->db->query($sql);
	}
	
	function main_category($main_category_id=0){
	
		$sql="select * from ccategory_to_ccategory as ctc
	
    			left join content_category as c
    			on c.cc_id = ctc.sub_cc_id
    
    			where ctc.top_cc_id=".$main_category_id." and  c.language_id=".language_id()."  order by c.sort_order asc
    	
    			";
		 
		return $this->db->query($sql);
	}
	
	
	function category_tree($main_category_id=0){
	
		$M=$this->main_category($main_category_id);
		if($M->num_rows){
			foreach ($M->result_array() as $k=>$mc){
	
				$shape["info"]=$mc;
				$shape["sub"]=$this->category_tree($mc["sub_cc_id"]);
				$catgories[]=$shape;
			}
			return $catgories;
		}else{
			return null;
		}
	}
	
	function lucky_category($id){
		 
		return $this->db->where("sub_cc_id",$id)->get("ccategory_to_ccategory");
	}
	
	function content_to_category($id){
			
		return $this->db->where("content_id",$id)->get("content_to_category");
	}
	
}