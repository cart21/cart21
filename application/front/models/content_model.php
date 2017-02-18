<?php
class content_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    
    	
 
    
 	
    	
    /// plugin latest_content ///
    function latest_content($limit=10,$language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
     	$this->db->where("language_id",$language_id);
    
    return $this->db->where("status",1)->order_by("content_id","desc")->limit($limit)->get("content");
    }
	/// plugin latest_content ///
    
    			function contents($language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
     	$this->db->where("language_id",$language_id);
    
    return $this->db->where("status",1)->get("content");
    }
    
	function content($content_id){
		
		$this->db->where("status",1);
    
    return $this->db->where("content_id",$content_id)->get("content");
    }

    function content_opt($data){
    	$this->db->where("status",1);
    	return $this->db->where($data)->get("content");
    }

    function related_contents($data_arr,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=f_language_id();
    	}
    	
    	$this->db->where("language_id",$language_id);

    	$this->db->where("status",1);
    	
    	return $this->db->where_in("c_id",$data_arr)->get("content");
    }
    
    function content_ids($content_category_id){
       
    return $this->db->where("content_category_id",$content_category_id)->get("content_to_category");
    }
    
     
    function content_categories($language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
     	$this->db->where("language_id",$language_id);
    
     return $this->db->where("status",1)->order_by("sort_order","asc")->get("content_category");
    }
    
    function content_types($language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
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
    
   

	function content_category_ids($content_id){
	
	return  $this->db->where("content_id",$content_id)->get("content_to_category");
	}
	
	function content_category_left($content_id){
	
		$this->db->select("*")->from("content_to_category");
		$this->db->join("content_category","content_to_category.content_category_id=content_category.cc_id","left");
		
		$this->db->where("content_to_category.content_id",$content_id);
	return  $this->db->get();
	}
	
	
	
	function main_category($main_category_id=0){
	
		$sql="select * from ccategory_to_ccategory as ctc
	
    			left join content_category as c
    			on c.cc_id = ctc.sub_cc_id
	
    			where ctc.top_cc_id=".$main_category_id." and  c.language_id=".f_language_id()."  order by c.sort_order asc
   
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
	
}