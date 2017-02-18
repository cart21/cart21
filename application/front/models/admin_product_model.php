<?php
class admin_product_model extends CI_Model {


	function __construct(){

        parent::__construct();
        
    }
    
    function products($data=""){
    
    	if(!empty($data)){
    	$this->db->where($data);
    	}
    return $this->db->get("product");
    }
    
    function products_img(){
    
    	$sql="select * from product as p
   
    	left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=p.product_id  where p.status=1";
    
    	return $this->db->query($sql);
    }
    
    function product_img($product_id){
    
    	$sql="select * from product as p
  
    	left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=p.product_id  where p.status=1 and p.product_id=".$product_id;
    
    	return $this->db->query($sql);
    }
    
    function product($product_id){
    
    
    return $this->db->where("product_id",$product_id)->get("product");
    }
    
    function product_image($product_id){
    
     return $this->db->where("product_id",$product_id)->get("product_image");
    }
    
    function plugin_download($product_id){
    
    	return $this->db->where("product_id",$product_id)->get("plugin_download");
    }
    
    function delete_image($product_id){
    
    	$image= $this->product_image($product_id);
    	if($image->num_rows){
    		 
    		foreach ($image->result_array() as $row ){
    			unlink($_SERVER["DOCUMENT_ROOT"].'/'.$row["image_loc"]);
    			$this->db->where("product_image_id",$row["product_image_id"])->delete("product_image");
    		}
    	}
    }
    function product_categories($language_id=null){
    
     	if(! $language_id){
     		$language_id=$_SESSION["cart21_language"]["language_id"];
     	}
     return $this->db->where(array("language_id"=>$language_id,"status"=>1))->get("product_category");
    }
    
    function product_category($category_id){
    
     return $this->db->where("product_category_id",$category_id)->get("product_category");
    }
    
    
    
    function product_feature_types($language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
     return $this->db->where(array("language_id"=>$language_id,"status"=>1))->get("product_feature_type");
    }
    
    function product_feature_type($product_feature_type_id){
    
     return $this->db->where("product_feature_type_id",$product_feature_type_id)->get("product_feature_type");
    }
    
    function product_feature($product_feature_id){
    
    return $this->db->where_in("product_feature_id",$product_feature_id)->get("product_feature");
    }
    
    function product_features($feature_type,$language_id=null){
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	
    	$this->db->where("feature_type",$feature_type);
    	$r=$this->db->where(array("language_id"=>$language_id,"status"=>1))->get("product_feature"); 
    	dbg($this->db->last_query());
    return $r;
    }
    
    function product_to_features($product_id,$language_id=null){
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	
    $sql="SELECT * FROM `product_to_feature` as ptf 
    		
    left join product_feature as pf 
    on pf.f_id=ptf.product_feature_id and language_id=".$language_id."
    
    where ptf.product_id=".$product_id." order by ptf.product_feature_id asc";
    
    return $this->db->query($sql);
    }
    
    
    function delete_product_category($product_id){
    
    	$this->db->where("product_id",$product_id)->delete("product_to_category");
    
    }
    
    function product_category_ids($product_id){
    
    	return  $this->db->where("product_id",$product_id)->get("product_to_category");
    }
    
    
    function product_related($p_a,$language_id=null){
    	
    		if(! $language_id){
    			$language_id=$_SESSION["cart21_language"]["language_id"];
    		}
    	
    	$sql="select * ,p.title as title, p.product_id as product_id from product_related as pr
    
    			inner join product as p
    			on p.pl_id=pr.product_idb and p.language_id=".$language_id."
    
    			left join (SELECT * FROM `product_image` group by product_id  order by sort_order asc ) as pi
    			on pi.product_id=pr.product_idb
    
    		where pr.product_ida=".$p_a;
    	 
    	return $this->db->query($sql);
    }
    
    
    function pl_group($pl_id){
    	
    	$sql="SELECT 
    			l.image as image,
    			l.language_id as language_id, 
    			p.pl_id as pl_id,
    			p.product_id as product_id
    			
    	FROM `language` as l
    	
		left join product as p
		on p.language_id=l.language_id and pl_id=".$pl_id."
    	
		where l.status=1 ";
    	 
    	return $this->db->query($sql);
    }
    
    function cl_group($cl_id){
    	 
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			pc.cl_id as cl_id,
    			pc.product_category_id as product_category_id
    
    	FROM `language` as l
   
		left join product_category as pc
		on pc.language_id=l.language_id and cl_id=".$cl_id."
   
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    function bl_group($bl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			pb.bl_id as bl_id,
    			pb.product_brand_id as product_brand_id
    
    	FROM `language` as l
  
		left join product_brand as pb
		on pb.language_id=l.language_id and bl_id=".$bl_id."
  
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    function propertyl_group($pl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			pp.pl_id as pl_id,
    			pp.product_property_id as product_property_id
    
    	FROM `language` as l
    
		left join product_property as pp
		on pp.language_id=l.language_id and pl_id=".$pl_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    function ft_group($ft_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			ft.ft_id as ft_id,
    			ft.product_feature_type_id as product_feature_type_id
    
    	FROM `language` as l
    
		left join product_feature_type as ft
		on ft.language_id=l.language_id and ft_id=".$ft_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    
    function f_group($f_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			f.f_id as f_id,
    			f.product_feature_id as product_feature_id
    
    	FROM `language` as l
    
		left join product_feature as f
		on f.language_id=l.language_id and f_id=".$f_id."
    
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }
    

}