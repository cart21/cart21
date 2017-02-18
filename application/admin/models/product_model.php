<?php
class product_model extends CI_Model {


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
     		$language_id=language_id();
     	}
     return $this->db->where(array("language_id"=>$language_id))->order_by("title","asc")->get("product_category");
    }
    
    function product_category($category_id){
    
     return $this->db->where("product_category_id",$category_id)->get("product_category");
    }
    
    
    
    function product_feature_types($language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
     return $this->db->where(array("language_id"=>$language_id,"status"=>1))->order_by("sort_order","asc")->get("product_feature_type");
    }
    
    function product_feature_types_opt($data=null){
    
    	if(! isset($data["language_id"])){
    		$language_id=language_id();
    	}
    	 
    	$sql="select * from product_feature_type as pft
    	left join product_type as pt
    	on pt.pl_id=pft.product_type_id and pt.language_id={$language_id}
    	where 	pft.language_id={$language_id} and pft.status=1
    	";
    	 
    	if( isset($data["product_type_id"]) and ! is_null($data["product_type_id"]) ){
    
	    	if( is_array($data["product_type_id"]) and isset($data["product_type_id"][0])){
	    		$sql.=" and pft.product_type_id in (".implode(",",$data["product_type_id"]) .") ";
	    	}elseif(is_numeric($data["product_type_id"])){
	    		$sql.=" and pft.product_type_id in (".$data["product_type_id"].") ";
	    	}else{
	    		$sql.=" and pft.product_type_id in (0) ";
	    	}
    	}
    	
    	if( isset($data["main_type"])){
    		
    		$sql.=" and pft.ft_id not in( select sub_type_id from product_feature_type  )" ;
    	}
    	 
    	$sql.=" order by pft.sort_order asc ";

    	return $this->db->query($sql);
    }
    
    function product_feature_type($product_feature_type_id){
    
     return $this->db->where("product_feature_type_id",$product_feature_type_id)->get("product_feature_type");
    }

    function product_feature($product_feature_id){
    
    	return $this->db->where_in("product_feature_id",$product_feature_id)->get("product_feature");
    }

    function product_feature_opt($data=null){
    
    	if(! isset($data["language_id"])){
    		$language_id=language_id();
    	}
    	
    	return $this->db->where($data)->get("product_feature");
    }
    
    function product_features($feature_type,$language_id=null){
    	if(! $language_id){
    		$language_id=language_id();
    	}
    	
    	$this->sub_feature_type=array();
    	$sub_type=$this->sub_feature_types($feature_type);
    	
    		foreach ($sub_type as $t=>$f){
    			
    			$title[]="f{$t}.feature_title";
    			$f_id[]="f{$t}.f_id";
    			$union_table[]="(SELECT * FROM `product_feature` WHERE `feature_type`={$f} and status=1 and language_id={$language_id}) as f{$t}";
    		}
    		
    		$d4=array("0"=>0,"1"=>0,"2"=>0,"3"=>0);
    	
    		$f_id=array_merge($f_id,array_diff_key($d4,$f_id));
    	
    		$sql="SELECT
				concat(".implode(", ' > ',",$title).") as feature_title,
				f0.`product_feature_id`,
				concat(".implode(", '-',",$f_id).") as f_id,
				f0.`language_id`,
				f0.`feature_type`,
				f0.`status`
    		
				FROM ";
    		
    		$sql.=implode(",",$union_table);
    		
    		$sql.=" where 1  order by f0. `feature_title`";
    		$r=$this->db->query($sql);
    	
    
    return $r;
    }
    
    function sub_feature_types($ft_id){
    	
    	$this->db->where(array("language_id"=>language_id()));
    	$r_sub=$this->db->where_in("ft_id",$ft_id)->get("product_feature_type");
    	
    	if($r_sub->num_rows){
    		$this->sub_feature_type[]=$r_sub->row()->ft_id;
    	
    		if($r_sub->row()->sub_type_id>0){
    			
    			$this->sub_feature_types($r_sub->row()->sub_type_id);
    			
    		}else{
    			
    			return$this->sub_feature_type;
    		}
    		
    	return $this->sub_feature_type;
    		
    	}
    	
    	
    }
    
    function product_to_features($product_id,$language_id=null){
    	if(! $language_id){
    		$language_id=language_id();
    	}
    	
    $sql="SELECT *, concat(ptf.product_feature_id,'-',f1,'-',f2,'-',f3) as f_id FROM `product_to_feature` as ptf 
    		
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
    			$language_id=$_SESSION["cart21_a_language"]["language_id"];
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
    function banner_group($bl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			pc.bl_id as bl_id,
    			pc.banner_id as banner_id
    
    	FROM `language` as l
  
		left join banner as pc
		on pc.language_id=l.language_id and bl_id=".$bl_id."
  
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
    
    function ptl_group($pl_id){
    
    	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			p.pl_id as pl_id,
    			p.product_type_id as product_type_id
    
    	FROM `language` as l
  
		left join product_type as p
		on p.language_id=l.language_id and pl_id=".$pl_id."
  
		where l.status=1 ";
    
    	return $this->db->query($sql);
    }

    
    function main_category($main_category_id=0){
    	
    $this->db->where("language_id",language_id());
    $this->db->where("main_category_id",$main_category_id);
     
    return $this->db->order_by("sort_order","asc")->get("product_category");
	}


	function product_category_tree($main_category_id=0){
		
		if($this->main_category($main_category_id)->num_rows){
			foreach ($this->main_category($main_category_id)->result_array() as $k=>$mc){
				
				$shape["info"]=$mc;
				$shape["sub"]=$this->product_category_tree($mc["cl_id"]);			
				$catgories[]=$shape;
			}
			return $catgories;
		}else{
			return null;
		}
	}
	
	function product_types(){
	
		$this->db->where("language_id",language_id());
	
		return $this->db->get("product_type");
	}
	
	
	
	function product_type_feature_tree($data=null){
	
		$data_tree=array();
		$product_type_id=isset($data["product_type_id"]) ? $data["product_type_id"] : "null";
		$product_feature_types=$this->product_feature_types_opt(array("product_type_id"=>$product_type_id,"main_type"=>1))->result_array();

		foreach ( $product_feature_types as $pt){
				
			$type["info"]=$pt;
			$type["features"]=$this->product_features($pt["ft_id"])->result_array();
			$data_tree[]=$type;
		}
	
		return $data_tree;
	}
	
	function delete_product_type($pl_id_arr){
	
		$feature_types=$this->product_model->product_feature_types_opt(array("product_type_id"=>array($pl_id_arr)));
		
		if($feature_types->num_rows){
				
			foreach ($feature_types->result_array() as $f){
	
				$this->delete_product_feature_type($f["ft_id"]);
			}
		}
	
		$this->db->where_in("pl_id",$pl_id_arr)->delete("product_type");
	
	
	}
	
	function delete_product_feature_type($ft_id_arr){
		
		$features=$this->product_features($ft_id_arr);
		
		if($features->num_rows){
			
			foreach ($features->result_array() as $f){
				
				$this->delete_product_feature($f["f_id"]);
			}
		}
		
		$this->db->where_in("ft_id",$ft_id_arr)->delete("product_feature_type");
		
		
	}
	
	function feature_relation($f_id){
		
		return $this->db->where("product_feature_id",$f_id)->get("product_to_feature");
	}
	
	function delete_product_feature($f_id){
	
		
		/// product stock and price ///
		$feature_re=$this->feature_relation($f_id);
		
		if($feature_re->num_rows){
			
			foreach ($feature_re->result_array() as $f ){
				if($f["selected"]=="0"){
					$this->db->query(" update product set number=number-{$f["number"]} where pl_id={$f["product_id"]}");
				}elseif($f["selected"]=="1"){
					$this->db->query(" update product set price=price-{$f["add_price"]} where pl_id={$f["product_id"]}");
				}
			}
		}
		/// product stock and price ///
		
		$this->db->where_in("f_id",$f_id)->delete("product_feature");
		
		$this->db->where_in("product_feature_id",$f_id)->delete("product_to_feature");
	}
	
}