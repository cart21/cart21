<?php

class modules {

	var $CI;

	function __construct(){

		 $this->CI =& get_instance() ;
		 $this->errors="";
		 $this->success="";
		
	
	}

	function logged_in(){
	
		if ($this->CI->sessiondd->userdata('admin') ){
			return true;
			 }else { 
			 return false;
			 }
		
	}
	
	function onlyLoginUser(){

		  if(!$this->logged_in()) {
		
		redirect('admin/login');
		exit;
		}
		
	}
	
	//// MODULES ////
	

	
	function support_list($smarty,$supports) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
		
		if($this->data['Total']>0){
			
		 $this->data['r_titles']=$this->get_title($supports->row_array());
			
			foreach( $supports->result_array() as $support){
			
				
				$support["user_id"]=$this->CI->User->customer2($support["user_id"]);
				$support["subject"]="<a href='/admin/support/ticket/".$support["support_id"]."' >".$support["subject"]." </a>";
				
				$support["date_added"]=date("d/m/Y H:i",$support["date_added"]);
				
				
				$this->data['supports'][]=$support;
			}
		}
			
	   		
	   	foreach($this->data as $key=>$value ){
	   	$smarty->assign($key,$value);
	   	}
	return  $smarty->fetch('support/support_list.tpl');
	
	}

	function get_customer($smarty,$user=""){
	
		if($user!=""){
			$data["page"]="edit";
			$birthday=$this->CI->quick->set_date2($user["birthday"]);
			$user["month"]=$birthday["mon"]; 
			$user["year"]=$birthday["year"]; 
			$user["day"]=$birthday["mday"]; 
			
			$user["telephone2"]=$user["telephone"];
			$user["note1"]=$user["note"];
			$data["user"]=$user;
			
    	}else{
    	
    	/*
    		$user["firstname"]=""; 
    		$user["lastname"]="";
    		$user["email"]="";
    		$user["telephone"]="";
    		$user["note"]="";
    		
    		$user["month"]=0; 
			$user["year"]=0; 
			$user["day"]=0; 
			$data["user"]=$user;
			*/
			
			$data["page"]="add";
			$data["user"]=$this->CI->input->post();
			
			
    	}
    	
    	$data["COUNTRY"]=$this->CI->quick->array_column($this->CI->db->order_by("order_list","desc")->get("country_list")->result_array() ,"turkish","country_code");

	foreach($data as $key=>$value ){
   	$smarty->assign($key,$value);
   	}
	return $smarty->fetch('account/costumer_form.tpl');
	exit;
	}

	
	
	function customer_list($smarty,$customers) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($this->data['Total']>0){
	
	 $this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $customers->result_array() as $customer){
		
			$customer["birthday"]=date('d/m/Y',$customer["birthday"]);
			$customer["date_added"]=date('d/m/Y H:i',$customer["date_added"]);
			
			$this->data['customers'][]=$customer;
		}
	}
		
   	foreach($this->data as $key=>$value ){	$smarty->assign($key,$value);	}
	return  $smarty->fetch('users/customer/customer_list.tpl');
	}

	function admin_list($smarty,$customers) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
				
			foreach( $customers->result_array() as $customer){
					
				$this->data['useradmins'][]=$customer;
			}
		}
	
		$this->CI->shape["admin_group"]="Group";
		$this->data['r_titles']=$this->get_title($this->CI->shape);
			
		foreach($this->data as $key=>$value ){	$smarty->assign($key,$value);	}
		return  $smarty->fetch('users/admin_list.tpl');
	}

	function admin_group_list($admin_groups) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
				
			foreach( $admin_groups->result_array() as $admin_group){
					
				$this->data['admin_groups'][]=$admin_group;
			}
		}
			
		return  $this->CI->smarty->fetch("users/admin_group_list.tpl",$this->data);
	}
	
	function customer_group_list($customer_groups) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			foreach( $customer_groups->result_array() as $customer_group){
					
				$this->data['customer_groups'][]=$customer_group;
			}
		}
			
		return  $this->CI->smarty->fetch("users/customer/customer_group_list.tpl",$this->data);
	}
	
	function logs_list($smarty,$logs) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($this->data['Total']>0){
	 $this->data['r_titles']=$this->get_title($logs->row_array());
		
		foreach( $logs->result_array() as $log){
		
			$log["date_added"]=date('d/m/Y  H:i',$log["date_added"]);
			
			if($log["user_type"]){
				
				$c=$this->CI->db->where("customer_id",$log["user_id"])->get("customer");
	 			if($c->num_rows){
	 			$log["user_id"]="front user ".$c->row()->firstname." - ".$c->row()->lastname ;
	 			}else{
	 				$log["user_id"]="non member";
	 			}
			}else{
			
				$admin=$this->CI->db->where("customer_id",$log["user_id"])->get("admin");
				if($admin->num_rows){
					$log["user_id"]="admin ".$admin->row()->firstname." - ".$admin->row()->lastname ;
				}
			}
				
			$this->data['logs'][]=$log;
		}
	}

   	foreach($this->data as $key=>$value ){
   	$smarty->assign($key,$value);
   	}
	return  $smarty->fetch('logs/logs_list.tpl');
	
	}
	


	function meta_list($smarty,$metas) {
	
		
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();

		foreach($this->CI->language_model->languages("all")->result_array() as $L){
			$languages[$L["language_id"]]=$L;
		}
	
		if($this->data['Total']>0){
	
		 $this->data['r_titles']=$this->get_title($metas->row_array());
	
			foreach( $metas->result_array() as $meta){
	
				$meta["type"]=$this->CI->meta_model->meta_type(array("meta_type_id"=>$meta["type"]))->row()->title;
				
				if(isset($languages[$meta["language_id"]])){
					$meta["lang_det"]=$languages[$meta["language_id"]];
				}else{
					$meta["lang_det"]=array("image"=>"img/default.png","short_name"=>$meta["language_id"]);
				}
				$this->data['metas'][]=$meta;
			}
		}
			
		foreach($this->data as $key=>$value ){
			$smarty->assign($key,$value);
		}
		return  $smarty->fetch('meta/meta_list.tpl');
	
	}
	

	function language_list($smarty,$languagess) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
		 $this->data['r_titles']=$this->get_title($languagess->row_array());
	
			foreach( $languagess->result_array() as $languages){
				 
				$this->data['languages'][]=$languages;
			}
		}
			
		foreach($this->data as $key=>$value ){
			$smarty->assign($key,$value);
		}
		return  $smarty->fetch('language/language_list.tpl');
	
	}
	
	function language_c_list($smarty,$language_cs) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		foreach($this->CI->languages->result_array() as $L){
			$languages[$L["language_id"]]=$L;
		}
		
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($language_cs->row_array());
	
			foreach( $language_cs->result_array() as $language_c){

			//	$this->language_id=$language_c["language_id"];
				//$language=array_filter($this->CI->languages->result_array(), function ($v){ return $v["language_id"]==$this->language_id; });	
				$language_c["lang_det"]=$languages[$language_c["language_id"]];
			
				$this->data['language_cs'][]=$language_c;
			}
		}
			
		foreach($this->data as $key=>$value ){
			$smarty->assign($key,$value);
		}
		return  $smarty->fetch('language/language_c_list.tpl');
	
	}
	
	function currency_list($smarty,$currencys) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($currencys->num_rows){
	
			$this->data['r_titles']=$this->get_title($currencys->row_array());
	
			foreach( $currencys->result_array() as $currency){
	
				$this->data['currencys'][]=$currency;
			}
		}
			
		foreach($this->data as $key=>$value ){
			$smarty->assign($key,$value);
		}
		return  $smarty->fetch('currency/currency_list.tpl');
	}
	
	function email_template_list($smarty,$email_template) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["languages"]=$this->CI->data["languages"];
	$this->data["L"]=$this->CI->data["L"];
	
	$this->data["settings_options"]=$this->CI->data["settings_options"];
	
	$this->data["message"]=$this->CI->get_filter_message();
	
	if($email_template->num_rows){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
	}
	foreach( $email_template->result_array() as $email_template ){
		
		$this->data['email_templates'][]=$email_template;
	}
	
   	foreach($this->data as $key=>$value ){
   	$smarty->assign($key,$value);
   	}
	return  $smarty->fetch('email_template/email_template_list.tpl');
	
	}
	
	function product_list($products) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];
	$this->data["currency_sign"]=$this->CI->data["currency_sign"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($products->num_rows){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $products->result_array() as $product){
			
			$product_images=$this->CI->product_model->product_image($product["pl_id"]);
			$product["image"]=$product_images->num_rows>0 ? $product_images->row()->image_loc :"";
			$product["slug"]=$product["slug"];
			$this->data['products'][]=$product;
		}
	}	
	
	return  $this->CI->smarty->fetch('product/product_list.tpl',$this->data);
	
	}
	
	function product_comment_list($product_categorys) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $product_categorys->result_array() as $category){
					
				$category["meta_type_id"]=$this->CI->meta_model->meta_type(array("meta_type_id"=>$category["meta_type_id"]))->row()->title;
	
				$c=$this->CI->user->customer2($category["customer_id"])->row();
				$category["customer_id"]=$c->firstname." ".$c->lastname;
	
				$category["date_added"]=date('d/m/Y H:i',$category["date_added"]);
	
				$this->data['product_comments'][]=$category;
			}
		}
	
		return  $this->CI->smarty->fetch('comment/comment_list.tpl',$this->data);
	}


	function product_category_list($product_categorys) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $product_categorys->result_array() as $category){
					
				$this->data['product_categorys'][]=$category;
			}
		}
	
	
		return  $this->CI->smarty->fetch('product/category_list.tpl',$this->data);
	}
	
	function product_type_list($product_types) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $product_types->result_array() as $product_type){
					
				$this->data['product_types'][]=$product_type;
			}
		}
	
	
		return  $this->CI->smarty->fetch('product/product_type_list.tpl',$this->data);
	}
	
	function country_list($countrys) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $countrys->result_array() as $country){
					
				$this->data['countrys'][]=$country;
			}
		}
	
		return  $this->CI->smarty->fetch('site_settings/country_list.tpl',$this->data);
	}
	
	function city_list($citys) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $citys->result_array() as $city){
					
				$this->data['citys'][]=$city;
			}
		}
	
		return  $this->CI->smarty->fetch('site_settings/city_list.tpl',$this->data);
	}
	
	function banner_list($banners) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach($banners->result_array() as $banner){
					
				$this->data['banners'][]=$banner;
			}
		}
	
		return  $this->CI->smarty->fetch('banner/banner_list.tpl',$this->data);
	}
	
	function product_property_list($product_property) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $product_property->result_array() as $property){
					
				$this->data['product_propertys'][]=$property;
			}
		}
	
		return  $this->CI->smarty->fetch('product/product_property_list.tpl',$this->data);
	}

	function product_brand_list($product_brand) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $product_brand->result_array() as $brand){
					
				$this->data['product_brands'][]=$brand;
			}
		}
	
		return  $this->CI->smarty->fetch('product/product_brand_list.tpl',$this->data);
	}
	
	function order_list($orders) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
		
		$this->data["order_shipping_status_all"]=$this->CI->data["order_shipping_status_all"];
		
		$this->data["message"]=method_exists($this->CI,"get_filter_message") ? $this->CI->get_filter_message() : "";
	
		if($orders->num_rows){
	
		$this->data['r_titles']=$this->get_title($this->CI->shape);
		
			foreach( $orders->result_array() as $order){
			
			$customer=$this->CI->user->customer2($order["customer_id"]);
			if($customer->num_rows){
			$order["customer_id"]=$customer->row()->firstname." ".$customer->row()->lastname;
			}else{
				$order["customer_id"]=$this->data["L"]["nonmember"];
			}
	
			$order["date_added"]=date("d-m-Y H:i",$order["date_added"]);

			$order["order_status"]=$this->CI->order_model->order_status($order["order_status"])->row();
			$order["bank_id"]=$this->CI->order_model->bank($order["bank_id"])->row();
			
			$this->data['orders'][]=$order;
			}
	
		}	

	return  $this->CI->smarty->fetch('order/order_list.tpl',$this->data);
	}
	
	function content_list($contents) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($this->data['Total']>0){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $contents->result_array() as $content){
			
			$content_type=$this->CI->content_model->content_type($content["content_type_id"]);
			if($content_type->num_rows>0){
				$content["content_type_id"]=$content_type->row()->title;
			}else{
				$content["content_type_id"]="<p style='color:red'>Not selected</p>";
			}
			$this->data['contents'][]=$content;
		}
	}	
   	
	return  $this->CI->smarty->fetch('content/content_list.tpl',$this->data);
	
	}
	
	function positional_content_list($positional_contents) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $positional_contents->result_array() as $positional_content){
	
				$this->data['positional_contents'][]=$positional_content;
			}
		}
	
		return  $this->CI->smarty->fetch('content/positional_content_list.tpl',$this->data);
	
	}
	
	function content_category_list($content_categorys) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($this->data['Total']>0){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $content_categorys->result_array() as $content_category){
			
			$c=$this->CI->content_model->content_type($content_category["content_type_id"]);
			if($c->num_rows){
			$content_category["content_type_id"]=$c->row()->title;
			}else{
				$content_category["content_type_id"]="No Category";
			}
			
			$this->data['content_categorys'][]=$content_category;
		}
	}	
   		/*
   	foreach($this->data as $key=>$value ){
   	//$this->CI->smarty->assign($key,$value);
   	*/
	
	return  $this->CI->smarty->fetch('content/content_category_list.tpl',$this->data);
	
	}
	
	function content_type_list($content_types) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];
	$this->data["L"]=$this->CI->data["L"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($content_types->num_rows){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $content_types->result_array() as $content_type){
			
			$this->data['content_types'][]=$content_type;
		}
	}	
   		
	return  $this->CI->smarty->fetch('content/content_type_list.tpl',$this->data);
	
	}
	
	function main_tabs_list($main_tabss) {
	
	$this->data['Total']=$this->CI->data['Total'];
	$this->data['pagelink']=$this->CI->data['pagelink'];
	$this->data["settings"]=$this->CI->data["settings"];

	$this->data["message"]=$this->CI->get_filter_message();
	
	if($this->data['Total']>0){
	
	$this->data['r_titles']=$this->get_title($this->CI->shape);
		
		foreach( $main_tabss->result_array() as $main_tabs){
			$main_tabs["title"]="maintab".$main_tabs["main_tabs_id"];
			$this->data['main_tabss'][]=$main_tabs;
		}
	}	
	
	$this->data["L"]=$this->CI->data["L"];
	return  $this->CI->smarty->fetch('main_tabs/main_tabs_list.tpl',$this->data);
	
	}
	
	function settings_options_list($smarty,$rooms) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($rooms->num_rows){

		$this->data['r_titles']=$this->get_title($this->CI->shape);
		}
	
		foreach( $rooms->result_array() as $stt ){
	
			$this->data['settings_optionss'][]=$stt;
		}
		
		foreach($this->data as $key=>$value ){
		$smarty->assign($key,$value);
		}
	return  $smarty->fetch('site_settings/site_settings_list.tpl');
	
	}
	
	
	function product_feature_list($data) {
	
		$this->data=$this->CI->data;
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($data->num_rows){
	
		$this->data['r_titles']=$this->get_title($this->CI->shape);
		}
	
		foreach( $data->result_array() as $row ){
			
			$row["feature_type"]=$this->CI->product_model->product_feature_type($row["feature_type"])->row()->feature_type_title;
			$this->data['product_features'][]=$row;
		}
	
		foreach($this->data as $key=>$value ){
		$this->CI->smarty->assign($key,$value);
		}
	return  $this->CI->smarty->fetch('product/product_feature_list.tpl');
	
	}
	
	function product_feature_type_list($data) {
	
		$this->data=$this->CI->data;
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($data->num_rows){
	
		$this->data['r_titles']=$this->get_title($this->CI->shape);
		}
	
		foreach( $data->result_array() as $row ){
	
			$this->data['product_feature_types'][]=$row;
		}
	
		foreach($this->data as $key=>$value ){
		$this->CI->smarty->assign($key,$value);
		}
	return  $this->CI->smarty->fetch('product/product_feature_type_list.tpl');
	
	}
	
	
	
	
	function plugin_list($plugin) {
	
		$this->data['Total']=$this->CI->data['Total'];
		$this->data['pagelink']=$this->CI->data['pagelink'];
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["L"]=$this->CI->data["L"];
	
		$this->data["message"]=$this->CI->get_filter_message();
	
		if($this->data['Total']>0){
	
			$this->data['r_titles']=$this->get_title($this->CI->shape);
	
			foreach( $plugin->result_array() as $p){
					
				$p["plugin_type_id"]=$this->CI->data["plugin_types"][$p["plugin_type_id"]];
				$this->data['plugins'][]=$p;
			}
		}
	
		return  $this->CI->smarty->fetch('plugins/plugin_list.tpl',$this->data);
	}
	
		
	//// MODULES ////
	

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