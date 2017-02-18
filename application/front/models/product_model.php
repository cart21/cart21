<?php
class product_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
    function products(){
    
    	$sql="select * from product as p
    	
    	left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=p.product_id  where p.status=1";
    		
    return $this->db->query($sql);
    }
    
    function product_type($data){
    	 
    	return $this->db->where($data)->get("product_type");
    }
    
    function product($product_id){
    
    return $this->db->where(array("product_id"=>$product_id))->get("product");
    }
    function product_opt($data){
    
    	$this->db->where($data);
    	return $this->db->where("status",1)->get("product");
    }
    
    function products_full($product_id){
    
    	$sql="select * from product as p
    	
    	left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=p.product_id  where p.status=1 and p.product_id=".$product_id;
    		
    return $this->db->query($sql);
    }
    
    
    function product_image($product_id){
     return $this->db->where("product_id",$product_id)->order_by("default_img","desc")->get("product_image");
    }
    function product_categories_p($product_id,$language_id=null){
    
    	if(! $language_id){
    		$language_id=f_language_id();
    	}
    	
    	$sql="select * from product_category as c
    
    				left join product_to_category as pc
    				on pc.category_id=c.cl_id 
    
    				where c.language_id=".$language_id." and pc.product_id=".$product_id." order by cl_id";
    
    	return $this->db->query($sql);
    }
    
    function product_categories($option=null){
    	
    	$this->db->where("status",1);
    	$this->db->where("language_id",f_language_id());
    	
    	if(isset($option["no_left"])){
    	
    		$this->db->where( "(no_left<>1 or no_left is null)");
    	}elseif(isset($option["no_top"])){
    	
    		$this->db->where( "(no_top<>1 or no_top is null)");
    	}
    	
    	$this->db->where("main_category_id",0);
    	
     return $this->db->order_by("sort_order","asc")->get("product_category");
    }
    
    function product_category($category_id){
    
     return $this->db->where("product_category_id",$category_id)->get("product_category");
    }
    
    function product_price($product_id){
    
    	$info=array();
    	$product=$this->product($product_id)->row_array();
    	
    	$info["price"]=$product["price"];
    	$info["discount_type"]=$product["discount_type"];
    	$info["amount"]=0;
    	
    	if($product["free"]){
    		$info["price"]=0;
    	}else{
    			
    		if($product["discount_type"]>0){
    		
    			if($product["discount_type"]==1){
    				$info["amount"]=$product["discount"];
    			}else{
    				$info["amount"]= ($product["discount"]*$info["price"])/100;
    			}
    			
    		}else{
    			
    		}
    	}
    	
     return $info;
    }
    
    
    function product_comments($product_id){
    
    	$sql="select * from product_comment as pc
    	
    	left join customer as c
    	on c.customer_id=pc.customer_id  where pc.status=1 and pc.product_id=".$product_id." order by pc.product_comment_id desc ";
    		
    return $this->db->query($sql);
    }
    
    /// product Features ///
    function product_feature_types(){
    
     return $this->db->get("product_feature_type");
    }
    
    function product_feature_type($product_feature_type_id,$language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	$this->db->where_in("language_id",$language_id);
    	
     return $this->db->where_in("ft_id",$product_feature_type_id)->order_by("sort_order","asc")->get("product_feature_type");
    }
    
    function product_feature($product_feature_id){
    
    return $this->db->where("language_id",f_language_id())->where_in("f_id",$product_feature_id)->get("product_feature");
    }
    
    function product_features($feature_type){
    	
    	$this->db->where("feature_type",$feature_type);
    	$r=$this->db->where("status",1)->get("product_feature"); 
    return $r;
    }
    
    function product_to_features($product_id="",$feature_type="",$language_id,$selected,$groupping=true){
    	$group_by="";
    	$order_by="";
    	
    	if($feature_type){
    	
    	$sql="SELECT * , 
    			
    		concat(ptf.product_feature_id,'-',ptf.f1,'-',ptf.f2,'-',ptf.f3) as fkey, 
    		
    		case pf.`f_id`
		        when  ptf.`product_feature_id`  then '0'
		        when ptf.f1 then  '1' 
		        when ptf.f2 then  '2' 
		        when ptf.f3 then  '3' 
    		end as feature_level
    		
    				
    		FROM  product_feature as pf  
	
			left join  `product_to_feature` as ptf 
			on ptf.product_feature_id=pf.f_id or ptf.f1=pf.f_id or ptf.f2=pf.f_id or ptf.f3=pf.f_id  
		
			left join product_feature_type as pft
			on pft.ft_id=pf.feature_type and pft.language_id=".$language_id." 
	
		where pf.language_id=".$language_id." and pft.ft_id=".$feature_type;
		 
    	$order_by.=" order by pft.sort_order asc ";
    	}else{
    	
			$sql="SELECT * FROM   product_feature as pf  
		
			left join `product_to_feature` as ptf 
			on ptf.product_feature_id=pf.f_id or ptf.f1=pf.f_id or ptf.f2=pf.f_id or ptf.f3=pf.f_id 
		
			where pf.language_id=".$language_id." ";
			
			$order_by.="  order by pf.product_feature_id asc ";
	
    	}
    	
    	
    	///
    	if(in_array($selected,array("0","1"))){
	    	
	    	if($selected=="0"){
	    		$sql.=" and ptf.selected='{$selected}' and ptf.number>0  ";
	    	}else{
	    		$sql.=" and ptf.selected='{$selected}' ";
	    	}
    	}else{
    		$sql.=" and ptf.number>0 ";
    	}
    	///
    	
    	
    	if(is_array($product_id)){
    		
    		$sql.=" and  ptf.product_id in(".implode(",",$product_id).") ";
    		$group_by.=" group by ptf.product_feature_id,pf.f_id";
    	}elseif(is_numeric($product_id)){
    		$sql.=" and  ptf.product_id=".$product_id;
    		$group_by.=" group by ptf.product_feature_id,pf.f_id";
    	}
    	
    	if($groupping){
    	$sql.=$group_by;
    	}
    	
    	$sql.=$order_by;
    //	echo $sql."<br><br><br>";
    	$re=$this->db->query($sql);
    
    	return $re;
    
    }
    
 function get_product_feature_tree($product_id,$language_id,$selected){
    
		$result=null;
		$r=$this->product_to_features($product_id,"",$language_id,$selected);
	
		if($r->num_rows){
	
			$dd=array_column($r->result_array(),"feature_type");
		
			$type=$this->product_feature_type(array_unique($dd),$language_id);
	
			foreach($type->result_array() as $t){
		
			$result[$t["product_feature_type_id"]]["info"]=$t;
			$result[$t["product_feature_type_id"]]["features"]=$this->product_to_features(	$product_id, $t["ft_id"],$language_id,$selected	)->result_array();
			}
	
		}
    return ($result);
    }
    
    function sum_feature_price($product_id,$product_feature_id){
    
    	$this->db->where("product_id",$product_id);
    	$this->db->where_in("product_feature_id",$product_feature_id);
    	
    	$result=$this->db->select_sum("add_price")->get("product_to_feature");
    
		if($result->num_rows){
		 return $result->row()->add_price;
		}else{
		return 0;
		}
   
    }
    
    
    /// product Features ///

    
    

    /// cart ///
    function cart_products(){

    	$cart_proccessed["totaltax"]=0;
    	$products=null;
    	$cart_proccessed["shipping"]=false;
    	$cart_proccessed["check_stock"]=true;
    
    if($this->quick->logged_user()){
    	$sql="select *,p.title as title,c.price as price,c.discount as discount,t.rate as tax_rate,c.number as number from cart as c
   
    	left join product as p
    	on p.pl_id=c.product_id and language_id=".f_language_id()."
   
    	left join product_image as pi
    	on pi.product_id=c.product_id and default_img=1
    			
    	left join tax as t
    	on t.tax_id=p.tax_id
   
    	where c.customer_id=".$this->quick->logged_user()->customer_id;
    	 
    	$result=$this->db->query($sql);
    	 
    	if($result->num_rows){
    		
    		foreach($result->result_array() as $product){
    
    			if(! is_null($product["product_features"]) ){
    				 
    				$product["product_feature"]=unserialize($product["product_features"]);
    			
    				$product["product_feature"]=$this->product_feature($product["product_feature"]["features"])->result_array();
    			}else{
    				$product["product_feature"]="";
    			}
    			
    			$product["check_stock"]=$this->check_stock($product["pl_id"],$product["product_features"],$product["number"]);
    			
    			if($product["check_stock"]["result"]== false){ $cart_proccessed["check_stock"]=false;}
    			
    			$product["product_features_selected"]=$this->get_product_feature_tree($product["pl_id"],f_language_id(),'1');
    			if($product["tax_id"]>0){

    				$cart_proccessed["totaltax"]+=($product["price"]*$product["number"]*$product["tax_rate"])/100;
    			}
    			if($product["shipping"]){
    				$cart_proccessed["shipping"]=true;
    			}
    			
    			$products[]	=$product;
    		}
    	}else{
    		return null;
    	}
    	
    	$cart_proccessed["cart_summary"]=$this->product_model->cart_summary()->row_array();
    	
    }else{
    	
    	$cart=$this->sessiondd->userdata("cart");
    	
    	$cart_proccessed["cart_summary"]["total_number"]=0;
    	$cart_proccessed["cart_summary"]["total_price"]=0;
    	$cart_proccessed["cart_summary"]["total_pure"]=0;
    	$cart_proccessed["cart_summary"]["total_discount"]=0;
    	
    	if(isset($cart["products"])){
    	foreach ($cart["products"] as $cproduct){
    		
    		$cart_proccessed["cart_summary"]["total_number"]+=$cproduct["number"];
    		$cart_proccessed["cart_summary"]["total_price"]+=$cproduct["number"]*($cproduct["price"]-$cproduct["discount"]);
    		$cart_proccessed["cart_summary"]["total_pure"]+=$cproduct["price"];
    		$cart_proccessed["cart_summary"]["total_discount"]+=$cproduct["number"]*$cproduct["discount"];
    		
    		$product=$this->product_opt(array("pl_id"=>$cproduct["product_id"],"language_id"=>f_language_id()))->row_array();
    		$cproduct=array_merge($product,$cproduct);
    		if(! is_null($cproduct["product_features"]) ){
    			 
    			$cproduct["product_feature"]=unserialize($cproduct["product_features"]);
    			$cproduct["product_feature"]=$this->product_feature($cproduct["product_feature"]["features"])->result_array();
    		}else{
    			$cproduct["product_feature"]="";
    		}
    		
    		$cproduct["check_stock"]=$this->check_stock($cproduct["pl_id"],$cproduct["product_features"],$cproduct["number"]);
    		if($cproduct["check_stock"]==-1){ $cart_proccessed["check_stock"]=false;}
    		
    		
    	
    		$cproduct["product_features_selected"]=$this->get_product_feature_tree($cproduct["pl_id"],f_language_id(),'1');
    		
    		$images=$this->product_image($cproduct["product_id"]);
    		$cproduct["image_loc"]=$images->num_rows ? $images->row()->image_loc : "img/default.png";
    		
    		if($product["tax_id"]>0){
    				$tax=$this->tax($cproduct["tax_id"])->row_array();
    				$cart_proccessed["totaltax"]+=($cproduct["price"]*$cproduct["number"]*$tax["rate"])/100;
    		}
    		if($cproduct["shipping"]){
    			$cart_proccessed["shipping"]=true;
    		}
    		
    		$products[]	=$cproduct;
    	}
    	}
    }

    	$cart_proccessed["products"]= $products;
    	 
    	return $cart_proccessed ;
    }
    
    function cart_summary(){
    
    	$sql="select
    			sum(number) as total_number, sum(number*(price-discount)) as total_price, sum(number*(price)) as total_pure, sum(number*discount) as total_discount from cart where customer_id=".$this->quick->logged_user()->customer_id;
    	return $this->db->query($sql);
    }
    
    function check_stock($product_id,$features,$required_number){
    	
    	$result["result"]=true;
    	$features=unserialize($features);
    	//dbg2($features);
    	if(!empty($features["features"])){
    		
    		$feature_level=array("product_feature_id","f1","f2","f3");
    		
    		$this->db->where("product_id",$product_id);
    		$c_level=0;
    		foreach ($features["features"] as $id=>$f_id ){
    			
    			if($c_level<=$features["level"][$id]){
    			$this->db->where($feature_level[$features["level"][$id]] , $f_id);
    			}
    			$c_level=$features["level"][$id];
    			
    		}
    		
    		$check=$this->db->get("product_to_feature");
    		//dbg($this->db->last_query());
    	}else{
    		$check=$this->product_opt(array("product_id"=>$product_id));
    	}
    	
    	if($check->num_rows){
	    	if( $check->row()->number < $required_number){
	    		$result["result"]=false;
	    		$result["number"]=$check->row()->number;
	    	}else{
	    		$result["result"]=true;
	    	}
    	}else{
    		$result["result"]=false;
    	}
    	//dbg2($result);
    	return $result;
    	
    }
    
    function cart_clear(){
    
    	if($this->quick->logged_in() ){
    	$this->db->where("customer_id",$this->quick->logged_user()->customer_id)->delete("cart");
    	 
    	return $this->db->affected_rows();
    	}else{
    		$sess=$this->sessiondd->all_userdata();
    		unset($sess["cart"]["products"]);
    		$this->sessiondd->set_userdata("cart",$sess["cart"]);
    		return 1;
    	}
    }
    
    /// cart ///
     
    
    
    /// orders ///
    
    function order($order_id){
    
    	if($this->quick->logged_in() ){
    		$customer_id=$this->quick->logged_user()->customer_id;
    	}else{
    		

    		$cart=$this->sessiondd->userdata("cart");
    		$customer_id=$cart["customer_id"];
    	}
    	
    	$sql="select * ,o.date_added as date_added from `order` as o
   
    			left join customer as c
    			on c.customer_id=o.customer_id
    
    		where o.order_id=".$order_id." and o.customer_id=".$customer_id;
    
    	return $this->db->query($sql);
    }
    
    function update_order($order_id,$data){
    	
    	$this->db->where("order_id",$order_id)->update("order",$data);
    }
    
    function order_products($order_id){
    

    	if($this->quick->logged_in() ){
    		$customer_id=$this->quick->logged_user()->customer_id;
    	}else{
    	
    	
    		$cart=$this->sessiondd->userdata("cart");
    		$customer_id=$cart["customer_id"];
    	}
    	 
    	
    	$sql="select *,op.number as number,op.discount as discount from order_product as op
    			left join product as p
    			on p.product_id=op.product_id
    
    			left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=op.product_id
    
    		where op.order_id=".$order_id." and op.customer_id=".$customer_id;
    
    	return $this->db->query($sql);
    }
    
    function get_my_orders(){
    	 
    	$this->db->where("customer_id",$this->quick->logged_user()->customer_id);
    	 
    	return $this->db->order_by("order_id","desc")->get("order");
    }
        
    function order_status_all(){
    	
    	return $this->db->where("status",1)->order_by("sort_order","asc")->get("order_status");
    }
    
    function order_status($id){
    	 
    	return $this->db->where("order_status_id",$id)->get("order_status");
    }

    
    
    function order_shipping_status_all(){
    
    	return $this->db->get("order_shipping_status");
    }
    
    function order_shipping_status($id){
    	 
    	 return $this->db->where("status_id",$id)->get("order_shipping_status");
    }
    
    
    
    function bank($id){
    
    	return $this->db->where("bank_id",$id)->get("bank");
    }
    
    
    function shipping_companies(){
    
    	return $this->db->where("status",1)->get("shipping_company");
    }
    
    function shipping_company($id){
    
    	return $this->db->where("company_id",$id)->get("shipping_company");
    }
    
    function shipping_price($id){
    	
    	return $this->shipping_company($id)->row()->price;
    }
    
    function taxes(){
    
    	return $this->db->get("tax");
    }
    
    function tax($id){
    
    	return $this->db->where("tax_id",$id)->get("tax");
    }
    
    
    function tax_price(){
    	 
    return 77;
    }
    

    /// orders ///
    
    
    
    
    function category_nums(){
    
    	$result=$this->db->query("select pc.category_id, count(*) as num from product_to_category as pc 
    			left join product as p
    			on p.pl_id=pc.product_id 
                
               where language_id=".f_language_id()." and p.status=1 group by pc.category_id ");
    
    	return  array_column($result->result_array() , "num","category_id");
    }
    
    function sub_categories($category_id,$option=null){
    
    	$this->db->where(array("status"=>1,"language_id"=>$_SESSION["cart21_language"]["language_id"]));
    	 
    	$this->db->where_in("main_category_id",$category_id);
    	
    	if(isset($option["no_left"])){
    	
    		$this->db->where( "(no_left<>1 or no_left is null)");
    	}elseif(isset($option["no_top"])){
    	
    		$this->db->where( "(no_top<>1 or no_top is null)");
    	}
    	 
    	$subs=$this->db->order_by("sort_order","asc")->get("product_category");
    	if($subs->num_rows){
    		return $subs->result_array();
    	}else{
    		return null;
    	}
    	 
    }
    
    function product_category_tree($option=null){
    
    	$data=array();
    
    	
    	foreach ($this->product_categories($option)->result_array() as $k=>$c){
    
    		$data[$k]=array(
    				"info"=>$c,
    				"sub"=>$this->sub_categories($c["cl_id"],$option)
    		);
    		
    		if($data[$k]["sub"]){
    			
    			foreach ($data[$k]["sub"] as $k1=>$c2){
    				
    				$data[$k]["sub"][$k1]=array(
    						"info"=>$c2,
    						"sub"=>$this->sub_categories($c2["cl_id"],$option)
    				);
    			}
    		}
    		
    	}
    	return $data;
    }
    
    function category_product_ids($category_id){
    
    	return  $this->db->where_in("category_id",$category_id)->get("product_to_category");
    }
    
    function property_products($property_id,$language_id=null){
    	 
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	 
    	$sql="select *,p.slug as slug from product as p
    
    			left join ( select product_id,image_loc from product_image group by product_id  )as pi
    			on pi.product_id=p.product_id
    
    			left join product_to_property as ptp
    			on ptp.product_id=p.pl_id
    
    			where p.status=1 and p.language_id=".$language_id." and  ptp.property_id=".$property_id;
    	 
    	return $this->db->query($sql);
    }
    
    function property($property_id){
    
    	return $this->db->where("product_property_id",$property_id)->get("product_property");
    
    }
    
    function properties($product_id,$language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	
    	$sql="select * from product_property as pp
    
    			left join product_to_property as ptp
    			on ptp.property_id=pp.pl_id
    
    			where pp.language_id=".$language_id." and  pp.status=1 and ptp.product_id=".$product_id;
    
    	return $this->db->query($sql);
    }

    function brands_own($language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	$this->db->where("language_id",$language_id);
    
    	return $this->db->where("status",1)->get("product_brand");
    
    }
    function brand($property_id){
    
    	return $this->db->where(array("product_brand_id"=>$property_id))->get("product_brand");
    
    }
    
    function brands($product_id,$language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	 
    	$sql="select * from product_brand as pp
    
    			left join product_to_brand as ptp
    			on ptp.brand_id=pp.bl_id
    
    			where pp.language_id=".$language_id." and  pp.status=1 and ptp.product_id=".$product_id;
    
    	return $this->db->query($sql);
    }
    
    function brand_products($brand_id,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    
    	$sql="select *,p.slug as slug from product as p
    
    			left join ( select image_loc,product_id from product_image where default_img=1 )as pi
    			on pi.product_id=p.pl_id
    
    			left join product_to_brand as ptp
    			on ptp.product_id=p.pl_id
    
    			where p.status=1 and p.language_id=".$language_id." and  ptp.brand_id=".$brand_id;
    
    	return $this->db->query($sql);
    }
    
    
    		
    			
    	
    			/// plugin latest product on the left ///
				
				function latest_product($limit,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=f_language_id();
    	}
    	 
    	
    	$sql="select *,if(p.discount_type=1, p.price-p.discount, (p.price-(p.discount*p.price)/100)  ) as price_d from product as p
    			
    			left join (select image_loc,product_id from product_image where default_img=1 ) as pi
    			
    			on pi.product_id=p.pl_id
    			
    			where p.language_id=".$language_id."  and p.status=1 order by p.pl_id desc limit ".$limit;
    	 
    	return $this->db->query($sql);
    }
				
				/// plugin latest product on the left ///
    			function product_related($p_a,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	
    	$sql="select * ,p.title as title, 	if(p.discount_type=1, p.price-p.discount, (p.price-(p.discount*p.price)/100)  ) as price_d
    			 from product_related as pr
    
    			inner join product as p
    			on p.pl_id=pr.product_idb
    
    			left join (SELECT * FROM `product_image` group by product_id  order by sort_order asc ) as pi
    			on pi.product_id=p.pl_id
    
    		where p.language_id=".$language_id." and  pr.product_ida=".$p_a." and p.status=1";
    
    	return $this->db->query($sql);
    }
    
}