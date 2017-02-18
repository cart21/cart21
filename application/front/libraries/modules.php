<?php
///admin
class modules {

	var $CI;

	function __construct(){
		//echo 'quick dddd';
		 $this->CI =& get_instance() ;
		 $this->errors="";
		 $this->success="";
		
	
	}
	
	function product_list($products) {
	
		/*
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		*/
	
		$this->data=$this->CI->data;
	
		$this->data["message"]=$this->CI->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $products->result_array() as $product){
					
				$category=$this->CI->product_model->product_category($product["category_id"])->num_rows()>0 ? $this->CI->product_model->product_category($product["category_id"])->row()->title :"not selected" ;
				$product["category_id"]=$product["category_id"]>0 ? $category :"none selected";
					
				$product_images=$this->CI->product_model->product_image($product["pl_id"]);
				$product["image_loc"]=$product_images->num_rows>0 ? $product_images->row()->image_loc :"";
				$product["slug"]=$product["slug"];
				$product["product_features_selected"]=$this->CI->product_model->get_product_feature_tree($product["pl_id"],f_language_id(),'1');
				
				$product["price_d"]=$this->CI->product_model->product_price($product["pl_id"]);
				
				/// product_list ///
				
				$this->data["products"][]=$product;
			}
		}
	
		return  $this->CI->smarty->fetch('product/product_list.tpl',$this->data);
	
	}
	
	
	function account_left(){
		$this->data["ci"]=& get_instance();
		$this->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array(4))->result_array(),"text_val","key_val");
		
		return   $this->CI->data["left_menu"]= $this->CI->smarty->fetch('account/left_menu.tpl',$this->data);
	}

///common functions ///
	function get_title($row_array){
 
		$keys=array_keys(array_intersect_key($row_array,$this->CI->shape));

		$translated=$this->translate_key($keys);
    
 	return $translated;
	 }

	function translate_key ($keys){
	
		foreach($keys as $key => $value ){
		
			$result["title"]=$this->CI->shape[$value];
			$result["key"]=$value;
			
			$get_uri_array=$this->CI->quick->get_query_array();
			$get_uri_array["orderby"]=$value;
			$result["link"]=base_url()."admin/".$this->CI->uri->segment(2)."/set_orderby/?".$this->CI->quick->create_uri_link($get_uri_array);
			
			
			$results[$key]=$result;
		}
	 return $results;
 	}
	
	
/*	
echo 'you are already logged in';
$sess_customer=$this->CI->sessiondd->userdata('customer');
$this->dbg2($sess_customer);
*/

}