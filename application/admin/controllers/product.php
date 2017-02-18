<?php
class product extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('product_model');
        $this->load->model('order_model');
		$this->load->model('product_property_model');
		$this->load->model('product_brand_model');
		$this->load->model('tabs_model');
		$this->load->library('image');
		
		$this->load->model('plugin_model');
		$this->load->library('page_position_lib');
		$this->data["plugin"]=$this->plugin=$this->plugin_model->plugin_by_key("product_module")->row();
		
        $this->meta_type=4;
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_id" => $this->data["L"]["id"],
    			"pl_id" =>  $this->data["L"]["id"],
    			"language_id" =>  $this->data["L"]["language"],
    			"product_id" =>  $this->data["L"]["id"],
    			"image" =>  $this->data["L"]["image"],
    			"title" =>  $this->data["L"]["title"],
    			"short_desc" =>  $this->data["L"]["description"],
    			"description" =>  $this->data["L"]["description"],
				"slug" =>  $this->data["L"]["link"],
				"keywords" => $this->data["L"]["keywords"],
				"price" =>  $this->data["L"]["price"],
				"status"=> $this->data["L"]["status"],
				"number"=> $this->data["L"]["stock"],
    		    "view_count"=> $this->data["L"]["view_count"],
    		    "last_update"=> $this->data["L"]["update"]
    		

			);
    
    }
    
    function where_work(){
    	
    	if(isset($this->form_post_product["language_id"])){
    		$this->db->where("language_id",$this->form_post_product["language_id"]);
    	}else{
    	
    		$this->db->where("language_id", language_id());
    	}
    	
		if(is_array($this->form_post_product)){
		
			$this->form_post_product_where=array_diff_key($this->form_post_product,$pattern=array(
					"slider"=>"slider",
					"slug"=>"slug",
					"keywords"=>"keywords",
					"description"=>"description",
					"title"=>"title",
					"category_id"=>"category_id"
					));
			$this->db->where($this->form_post_product_where);
			
			
		
			if(isset($this->form_post_product["slider"])){
				$price=explode(";",$this->form_post_product["slider"]);
				$this->db->where("price between ".$price[0]." AND ".$price[1]." ");
				
				$this->data["POST"]["price"]=$price[0]."$ - ".$price[1]."$";

				$this->data["price1"]=$price[0];
				$this->data["price2"]=$price[1];
				unset($this->data["POST"]["slider"]);
			}
			
			if(isset($this->form_post_product["category_id"])){
				$product_ids=array(null);
				$sub=$this->db->query("select * from product_category where main_category_id in(".implode(",",$this->form_post_product["category_id"]).")");
			
				$categories=$this->form_post_product["category_id"];
			
				if($sub->num_rows){
					$categories=array_merge($categories,array_column($sub->result_array(),"cl_id"));
				}
			
				$pc=$this->db->query(" select * from product_to_category where category_id in (".implode(",",$categories).") ");
			
				if($pc->num_rows){
					$product_ids=array_column($pc->result_array(),"product_id");
				}
					
			
				$this->db->where_in("pl_id",$product_ids);
			
			}
			
			if(isset($this->form_post_product["keywords"])){
				$this->db->like("keywords",$this->form_post_product["keywords"]);
			}
			
			if(isset($this->form_post_product["short_desc"])){
				$this->db->like("description",$this->form_post_product["short_desc"]);
			}

			if(isset($this->form_post_product["title"])){
				$this->db->like("title",$this->form_post_product["title"]);
			}
			if(isset($this->form_post_product["slug"])){
				$this->db->like("slug",$this->form_post_product["slug"]);
			}
			
			
			
		}else{
		$this->form_post_product=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){
		
		$this->permission->check_permission("view");
		
		$this->data["currency_sign"]=$this->db->where("currency_id",$this->data["settings"]["currency_id"])->get("currency")->row()->sign;
		
		$this->quick->Header("");
		
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_product=array_filter($this->input->post());
			if($this->input->post("status")==(-1)){
				$this->form_post_product["status"]=0;
			}
			$this->sessiondd->set_userdata('form_post_product',$this->form_post_product);
		}else{
		
			$this->form_post_product=$this->sessiondd->userdata('form_post_product') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=20;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product/index';
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 4;
		$config["num_links"] =6;// round($choice);
		//$config['cur_tag_open'] = '<b>';
		$config['last_link'] = 'Last';
		$config['first_link'] = "First";
		$config['prev_link'] = ' « ';
		$config['next_link'] = ' » ';
		
		
		$config['full_tag_open'] = '  <ul class="pagination pagination-sm no-padding no-margin pull-left">';
		$config['full_tag_close'] = ' </ul>';
		
		$config['cur_tag_open'] = ' <li><a href="#" class="bg-navy text-white"> ';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = ' <li class="text-navy">';
		$config['num_tag_close'] = '</li>';
		
		$config['first_tag_open'] = ' <li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = ' <li>';
		$config['last_tag_close'] = '</li>';
		
		
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		
		$this->pagination->initialize($config); 
		
		$this->data["pagelink"]=$this->pagination->create_links();
		/////////////  pagination  /////////////
		
		
		$this->where_work();
		
		///order by///
		
		$product_session=$this->sessiondd->userdata('product');
		
		if (	isset($product_session["orderby"])		){
		
		$this->db->order_by($product_session["orderby"], $product_session["orderby_order"]);
		}else{
		$this->db->order_by("product_id", "desc");
		}	
		///order by///
			
	
		//// filter*  ////
		
		$page=$this->uri->segment(4);
		
		if(!empty($page) and $page!="list" ){
 		
 		$per_page_start=$per_page*($page-1);
 		}else{
 		$page=0;
 		$per_page_start=$per_page*$page;
 		}
 		
 		
 		$products=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product");   ///segment 4 page
		
		$this->data["products"]=$this->modules->product_list($products);
		
		if($action=="list"){
		echo $this->data["products"];
		exit;
		}
		
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";

	$this->quick->Header("");
	
	$this->data["taxes"]=$this->order_model->taxes();
	$this->data["properties"]=$this->product_property_model->properties();
	$this->data["brands"]=$this->product_brand_model->brands();
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('title', $this->data["L"]["title"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('slug', $this->data["L"]["link"], 'trim|xss_clean|callback_check_slug');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data_product=$this->input->post();
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$data_product["status"]=1;
	 		}else {
	 		
	 			$data_product["status"]=0;
	 		}
	 		///
	 		
	 		$data_product=array_diff_key($this->data["POST"],array("ptf"=>1,"category_id"=>1,"property_id"=>1,"brand_id"=>1,"main_tabs_id"=>1,"plugin_to_page"=>2));
	 		
	 			$data_product["language_id"]=language_id();
	 			$data_product["last_update"]=$data_product["date_added"]=mktime();
	 			
		 		$this->db->insert("product",$data_product);
		 		
		 		$ids=$this->db->insert_id();
		 		
		 	
		 		
		 		// category //
		 		foreach($this->input->post("category_id") as $category_id){
		 		
		 			$this->db->insert("product_to_category",array("product_id"=>$ids,"category_id"=>$category_id));
		 		}
		 		// category //
		 		
		 		// property //
		 		if($this->input->post("property_id")){
			 		foreach($this->input->post("property_id") as $property_id){
			 		
			 			$this->db->insert("product_to_property",array("product_id"=>$ids,"property_id"=>$property_id));
			 		}
		 		}
		 		// property //
	
		 		// brand //
		 		if($this->input->post("brand_id")){
		 			foreach($this->input->post("brand_id") as $brand_id){
		 		
		 				$this->db->insert("product_to_brand",array("product_id"=>$ids,"brand_id"=>$brand_id));
		 			}
		 		}
		 		// brand //
		 		/// main tabs ///
		 		 
		 		if($this->input->post("main_tabs_id")){
		 			 
		 			foreach($this->input->post("main_tabs_id") as $main_tabs_id){
		 				 
		 				$this->db->query("replace into main_tabs_to_product  (main_tabs_id,product_id) values ('".$main_tabs_id."','".$ids."')");
		 			}
		 		}
		 		/// main tabs ///
		 		
		
		 		
		 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
		 		
		 		$meta_data["description"]=strip_tags($this->input->post("description"));
		 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
		 		$meta_data["link"]=strip_tags($this->input->post("slug"));
		 		$meta_data["language_id"]=$data_product["language_id"];
		 		$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
		 		$meta_data["class_routes"]="/product/index/".$ids;
		 		$meta_data["title"]=$this->input->post("title");
		 		$meta_data["type_l_id"]=$ids;
		 		
		 		$meta_data=$this->meta_model->insert_meta($meta_data);
		 		
		 		$this->db->where("product_id",$ids)->update("product",array("pl_id"=>$ids,"slug"=>$meta_data["link"]));
		 		 
		 		///plugin dynamic to page ///
		 		$this->page_position_lib->after_post(array("type_id"=>$ids,"plugin_id"=>$this->plugin->plugin_id));
		 		///plugin dynamic to page ///
		 		
		 		$this->quick_model->logs($ids." idli product added !");
	 			
	 		$this->upload($ids);
	 		
	 	redirect("admin/product/edit/".$ids."/".$ids);
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
	//$this->data["product_categories"]=$this->product_model->product_categories()->result_array();
	$this->data["product_categoriesb"]=$this->product_model->product_category_tree(0);
	$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
	$this->data["maintabs"]=$this->tabs_model->tabs();
	$this->data["product_types"]=$this->product_model->product_types();
	
	$this->page_position_lib->set_page_position_form(array("type_id"=>0,"plugin_id"=>$this->plugin->plugin_id));
	
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_form',$this->data);
	
    }
    
    function change_status(){

    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	
    	if($this->input->post("status")=="true"){
    		
    		$status=1;
    	}else {

    		$status=0;
    	}
    	
    	if($this->input->post("product_id")>0){
    		
    		$this->db->where("product_id",$this->input->post("product_id"))->update("product",array("status"=>$status ));
    		
    	}
    	
    	echo 1; exit;
    	
    }
    
    function edit($ids,$pl_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
	    
	    $this->quick->Header("");
	    
	    $this->data["product_id"]=$ids;
   		$this->ids=$ids;

   		$this->data["taxes"]=$this->order_model->taxes();
   	
		$this->data["POST"]=$this->input->post();
		
		if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title',$this->data["L"]["title"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('slug', $this->data["L"]["link"], 'trim|xss_clean|callback_check_slug_edit');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		$this->data["POST"]["last_update"]=mktime();
	 		
	 		$data_product=array_diff_key($this->data["POST"],array("ptf"=>1,"category_id"=>1,"property_id"=>1,"brand_id"=>1,"main_tabs_id"=>2,"plugin_to_page"=>2,"default_image"=>2));
	 		
	 		$this->db->where("product_id",$ids)->update("product",$data_product);
	 		
	 		$intersect_key=array(
	 				"price"=>1,
	 				"discount"=>1,
	 				"discount_type"=>1,
	 				"tax_id"=>1,
	 				"search_words"=>1,
	 				"catalog"=>1,
	 				"free"=>1,
	 				"shipping"=>1,
	 				"model"=>1,
	 				"unique_code"=>1,
	 				"product_type_id"=>1,
	 				"last_update"=>1,
	 				"date_added"=>1
	 		);
	 		
	 		$pl_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 
	 		foreach($this->language_model->languages()->result_array() as $l){
	 			
	 			$this->db->where(array("pl_id"=>$pl_id,"language_id"=>$l["language_id"]))->update("product",$pl_new_data);
	 		}
	 		
	 		$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		$this->product_relation($pl_id);
	 		
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		$meta=$this->meta_model->meta($meta_data);

	 		$meta_data["title"]=$this->input->post("title");
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
			$meta_data["language_id"]=$this->input->post("language_id");
			$meta_data["type_l_id"]=$pl_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			$meta_data["class_routes"]="/product/index/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data);
	 		$this->db->where("product_id",$ids)->update("product",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>$pl_id,"plugin_id"=>$this->plugin->plugin_id));
	 		///plugin dynamic to page ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		$this->upload($pl_id);
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->product_model->product($ids)->row_array();
		
		$this->product_language_create();
		
		//$this->data["product_categories"]=$this->product_model->product_categories($this->data["POST"]["language_id"])->result_array();
		$this->data["product_categoriesb"]=$this->product_model->product_category_tree(0);
		
		$this->data["product_image"]=$this->product_model->product_image($this->data["POST"]["pl_id"])->result_array();
		$this->data["product_feature_types"]=$this->product_model->product_feature_types($this->data["POST"]["language_id"])->result_array();
		$this->data["brands"]=$this->product_brand_model->brands($this->data["POST"]["language_id"]);
		$this->data["properties"]=$this->product_property_model->properties($this->data["POST"]["language_id"]);
		$this->data["maintabs"]=$this->tabs_model->tabs();
		$this->data["product_types"]=$this->product_model->product_types();
		
		$this->data["products_select"]=$this->product_model->products(array("status"=>1,"language_id"=>$this->data["POST"]["language_id"]))->result_array();
		$this->data["product_related"]=$this->product_model->product_related($this->data["POST"]["pl_id"],$this->data["POST"]["language_id"]);
		
		$lucky_ptf=$this->product_model->product_to_features($this->data["POST"]["pl_id"],$this->data["POST"]["language_id"]);
		$this->data["product_to_features"]=$lucky_ptf->result_array();
		//dbg($this->data["product_to_features"]);
		if($lucky_ptf->num_rows>0){
		
			$this->data["luck_features"]=array_column(array_filter($this->data["product_to_features"],function($v){ return ! $v["selected"]; } ),"f_id");
			$this->data["luck_selected"]=array_column(array_filter($this->data["product_to_features"],function($v){ return $v["selected"]; } ),"f_id");
			$this->data["luck_add_price"]=array_column($this->data["product_to_features"],"add_price","f_id");
			$this->data["luck_stock"]=array_column($this->data["product_to_features"],"number","f_id");
		}else {
			$this->data["luck_features"]=array();
			$this->data["luck_selected"]=array();
			$this->data["luck_add_price"]=array();
			$this->data["luck_stock"]=array();
		}

		$lucy_category=$this->product_model->product_category_ids($this->data["POST"]["pl_id"]);
		if($lucy_category->num_rows){
			$this->data["lucky_category"]=array_column($lucy_category->result_array(),"category_id");
		}else{
			$this->data["lucky_category"]=array();
		}
		
		$lucy_property=$this->product_property_model->product_property_ids($this->data["POST"]["pl_id"]);
		if($lucy_property->num_rows){
			$this->data["lucky_property"]=array_column($lucy_property->result_array(),"property_id");
		}else{
			$this->data["lucky_property"]=array();
		}

		$lucy_brand=$this->product_brand_model->product_brand_ids($this->data["POST"]["pl_id"]);
		if($lucy_brand->num_rows){
			$this->data["lucky_brand"]=array_column($lucy_brand->result_array(),"brand_id");
		}else{
			$this->data["lucky_brand"]=array();
		}
		
		$lucky_maintabs=$this->tabs_model->product_tabs($this->data["POST"]["pl_id"]);
		if($lucky_maintabs->num_rows){
			$this->data["lucky_maintabs"]=array_column($lucky_maintabs->result_array(),"main_tabs_id");
		}else{
			$this->data["lucky_maintabs"]=array();
		}
		
	
	
		$this->data["feature_tree"]=$this->product_model->product_type_feature_tree(array("product_type_id"=>$this->data["POST"]["product_type_id"]));
		//dbg(	$this->data["feature_tree"]);
		$this->data["pl_group"]=$this->product_model->pl_group($this->data["POST"]["pl_id"])->result_array();
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>$pl_id,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_form',$this->data);
    
	
    }
    
    function product_relation($ids){
    	$this->permission->check_permission("edit");
    	/// product Feature ///
	    	$total_stock=0;
	    	$total_add_price_selected=0;
	    	
	    	$pre_total=$this->db->query("select sum(add_price) as total from product_to_feature where selected='1' and product_id=".$ids);
	    	$pre_total=$pre_total->row()->total ? $pre_total->row()->total : 0;
	    	
	    	$pre_stock=$this->db->query("select sum(number) as total from product_to_feature where selected='0' and product_id=".$ids);
	    	$pre_stock=$pre_stock->num_rows ? $pre_stock->row()->total : 0;
	    	
	    	$this->db->where("product_id",$ids)->delete("product_to_feature");
	    	
	    	if($this->input->post("ptf")){
	    		$ptf=$this->input->post("ptf");
	    		
	    		/// features with add_price  
	    		if(isset($ptf["f_id"])){
		    		foreach($ptf["f_id"] as $f_id){
		    			$total_stock+=$ptf["stock"][$f_id];
		    			
		    			$f_ids=explode("-",$f_id);
		    			
		    			$this->db->insert("product_to_feature",array("product_id"=>$ids,"product_feature_id"=>$f_ids[0],"add_price"=>$ptf["add_price"][$f_id],"number"=>$ptf["stock"][$f_id],"f1"=>$f_ids[1],"f2"=>$f_ids[2],"f3"=>$f_ids[3]));
		    			
		    		}
	    		}else{
	    		$total_stock=$this->input->post("number")-$pre_stock;
	    		}
	    		
	    		/// selected 
	    		if(isset($ptf["selected"])){
	    			foreach($ptf["selected"] as $f_id){
	    				$total_add_price_selected+=$ptf["add_price"][$f_id];
	    				$f_ids=explode("-",$f_id);
	    				$this->db->query("replace into product_to_feature (product_id,product_feature_id,selected,add_price) 
	    						values({$ids},{$f_ids[0]},'1','".$ptf["add_price"][$f_id]."' )");
	    				//$this->db->insert("product_to_feature",array("product_id"=>$ids,"product_feature_id"=>$f_id,"selected"=>"1","add_price"=>$ptf["add_price"][$f_id]));
	    			}
	    		}
	    		
	    	}else{
	    		$total_stock=$this->input->post("number");
	    	}
	    	
	    	/// total add price,number calculation
	    	
	    	if(($total_add_price_selected-$pre_total)>=0){
	    		$this->db->query(" update product set number={$total_stock}, price=price + ".($total_add_price_selected-$pre_total)." where pl_id=".$ids);
	    	}else{
	    		$this->db->query(" update product set number={$total_stock}, price=price - ".abs($total_add_price_selected-$pre_total)." where pl_id=".$ids);
	    	}
	    	/// total add price,number calculation
	    	
    	
    	/// product Feature ///
    	
    	// category //
    	$this->product_model->delete_product_category($ids);
    	if($this->input->post("category_id")){
    	foreach($this->input->post("category_id") as $category_id){
    	
    		$this->db->insert("product_to_category",array("product_id"=>$ids,"category_id"=>$category_id));
    	}
    	}
    	// category //
    	
    	// property //
    	$this->product_property_model->delete_product_property($ids);
    	if($this->input->post("property_id")){
    		foreach($this->input->post("property_id") as $property_id){
    	
    			$this->db->insert("product_to_property",array("product_id"=>$ids,"property_id"=>$property_id));
    		}
    	}
    	// property //
    	
    	// brand //
    	$this->product_brand_model->delete_product_brand($ids);
    	if($this->input->post("brand_id")){
    		foreach($this->input->post("brand_id") as $brand_id){
    			$this->db->insert("product_to_brand",array("product_id"=>$ids,"brand_id"=>$brand_id));
    		}
    	}
    	// brand //
    	
    	/// main tabs ///
    	
    	$luck_tabs=$this->db->where("product_id",$ids)->get("main_tabs_to_product");
    	// delete tabs //
    	if($luck_tabs->num_rows){
    		$tab_order=array_column($luck_tabs->result_array() ,"sort_order","main_tabs_id");
    		//dbg($tab_order);
    		if($this->input->post("main_tabs_id")){
    			
    			$unlucky_tabs=array_diff(array_column($luck_tabs->result_array(),"main_tabs_id"),$this->input->post("main_tabs_id"));
    			
    			if(count($unlucky_tabs)>0 ){
    				$this->db->where_in("main_tabs_id",$unlucky_tabs);
    				 
    				$this->db->where("product_id",$ids)->delete("main_tabs_to_product");
    			}
    			
    		}else{
    			$this->db->where("product_id",$ids)->delete("main_tabs_to_product");
    		}
    	}
    	// delete tabs //
   
    	
    	if($this->input->post("main_tabs_id")){
    	
    		foreach($this->input->post("main_tabs_id") as $main_tabs_id){
    			$sort_order=isset($tab_order[$main_tabs_id]) ? $tab_order[$main_tabs_id] : 0 ;
    			$this->db->query("replace into main_tabs_to_product  (main_tabs_id,product_id,sort_order) values ('".$main_tabs_id."','".$ids."','".$sort_order."')");
    		}
    	}
    	/// main tabs ///

    	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			/// delete image file
			$images=$this->db->where_in("product_id",$this->input->post("product_id"))->get("product_image");
			foreach ($images->result_array() as $i ){
				$image_url=$i["image_loc"];
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/uploads/product/".$image_url)){
					$this->image->delete_img_thumb("/uploads/product/".$image_url);
				}
			}
			/// delete image file
			/// delete db image
			$this->db->where_in("product_id",$this->input->post("product_id"))->delete("product_image");
			
			/// delete meta ///
			$this->db->where("type", $this->meta_type);
			$this->db->where_in("type_l_id",$this->input->post("product_id"))->delete("meta");
			/// delete meta ///
			
			$this->db->where_in("pl_id",$this->input->post("product_id"))->delete("product");
			
			$this->db->where_in("product_id",$this->input->post("product_id"))->delete("product_to_category");
			$this->db->where_in("product_id",$this->input->post("product_id"))->delete("product_to_brand");
			$this->db->where_in("product_id",$this->input->post("product_id"))->delete("product_to_feature");
			$this->db->where_in("product_id",$this->input->post("product_id"))->delete("product_to_property");
			
			$this->quick_model->logs(implode(',',$this->input->post("product_id"))." idli products deleted ");
			$result="1";
		}else{
			$result="0";
		}
	   	echo $result;
    	exit;	
    }
    
    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="product_group"){
				$group=$this->db->where_in("product_group_id",$value)->get("product_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}elseif($key=="category_id"){
				$this->db->where("language_id",language_id());
				$group=$this->db->where_in("cl_id",$value)->get("product_category")->result_array();
				$message.= "<p><b>".$this->language_model->language_c_key("categories")."</b> : ".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}elseif($key=="language_id"){
				$group=$this->db->where_in("language_id",$value)->get("language")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"name") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product',"");
	redirect('admin/product');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_session=$this->sessiondd->userdata('product') ;
			
				$product_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product',$product_session);
			
				
				if(	isset($product_session["orderby_order"]) ){
				
					if($product_session["orderby_order"]=="asc" ){
					
						$product_session["orderby_order"]="desc";
					}else{
					
						$product_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product',$product_session);
			}
	
	redirect('admin/product');
	}
	
	function upload($product_id){

		if($_FILES["ImageFile"]["error"][0]!=4){
		
		
		$date_folder=date("Y/m/d",mktime());
		$dirpath="uploads/product/".$date_folder;
		create_dir($dirpath);
		
        $config['upload_path'] =$_SERVER["DOCUMENT_ROOT"]. "/".$dirpath;
       
		$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		$check_default=$this->db->query("select product_image_id from product_image where product_id={$product_id} and default_img=1 ");
		$default_img=$check_default->num_rows>0 ? 0 : 1;
		foreach($result["success"] as $image){
			
			$image_data=array(
			"image_loc"=>$date_folder.'/'.$image["file_name"],
			"product_id"=>$product_id,
			"title"=>str_replace($image["file_ext"],"",$image["client_name"]),
			"file_ext"=>substr($image["file_ext"],1),
			"default_img"=>$default_img,
			"date_added"=>mktime()
			);
			$this->db->insert("product_image",	$image_data	);
			$this->image->create_thumb("/uploads/product/".$image_data["image_loc"]);
			
		}
		
		}
    }
 
    function delete_image(){
    	$this->permission->check_permission("delete");
	    $image_url=$this->db->where(array("product_image_id"=>$this->input->post("file_id")) )->get("product_image");
	    $image_url=$image_url->row()->image_loc;
	    
	    $this->db->delete("product_image",array("product_image_id"=>$this->input->post("file_id")));
	   
	    $this->image->delete_img_thumb('/uploads/product/'.$image_url);
    exit;
    }
	
	function check_slug($link){

    	$meta=$this->meta_model->meta(array("link"=>$link));
    	
    	if($meta->num_rows>0){
    	
    	$this->form_validation->set_message('check_slug', 'The meta '.$meta->row()->link.' %s is already in use meta id:'.$meta->row()->meta_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}
	
	function check_slug_edit($link){
	
		$this->db->where("type <>",$this->meta_type);
		$this->db->where("type_id <>",$this->ids);
		
    	$meta=$this->meta_model->meta(array("link"=>$link));
    	
    	if($meta->num_rows>0){
    	
    	$this->form_validation->set_message('check_slug_edit', 'The meta '.$meta->row()->link.' %s is already in use meta id:'.$meta->row()->meta_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}	
	
	function features_select(){
		$this->permission->check_permission("view");
		
		if($this->input->is_ajax_request()){
		
			
			$result=$this->product_model->product_features($this->input->post("feature_type"),$this->input->post("language_id"));	
			
			$select='<option value="0"> select</option>';
			if($result->num_rows>0){
			
				foreach($result->result_array() as $f){
				
				$select.='<option value="'.$f["f_id"].'" > '.$f["feature_title"].'</option>';
				}
			$select.='';
			echo $select; exit;
			}else{
			
			echo ""; exit;
			}
			
			
		}
			
	}
	
	function add_related(){
	
		if($this->input->is_ajax_request() and $this->permission->check_permission("add") ){
	
			$response["error"]=0;
			$response["message"]="";
	
			$insert_data=array(
					"product_ida"=>$this->input->post("product_ida"),
					"product_idb"=>$this->input->post("product_idb"),
			);
	
			$check =$this->db->where($insert_data)->get("product_related");
	
			if($check->num_rows>0){
	
				$response["error"]=1;
				$response["message"]="The product is already assigned ?";
				echo json_encode($response);
				exit;
			}
	
			$this->db->insert("product_related",$insert_data);
	
			if($this->db->affected_rows()){
				$this->quick_model->logs("product ".$this->input->post("product_idb")." added as related to product id ".$this->input->post("product_ida")."");
				$response["product"]=($this->product_model->product_img($this->input->post("product_idb"))->row_array());
			}
	
			echo json_encode($response);
			exit;
				
		}
	}
	
	function remove_related(){
	
		
		if($this->input->is_ajax_request() and $this->permission->check_permission("delete")){
	
			$response["error"]=0;
			$response["message"]="";
	
			$insert_data=array(
					"product_ida"=>$this->input->post("product_ida"),
					"product_idb"=>$this->input->post("product_idb"),
			);
	
			$this->db->where($insert_data)->delete("product_related");
	
			echo 1;
			exit;
	
		}
	}
	
	function product_language_create(){
		
		$sql="SELECT l.language_id FROM `language` as l

			left join product as p
			on p.language_id=l.language_id and p.pl_id=".$this->data["POST"]["pl_id"]."
			
			where l.status=1 and ( p.product_id is null)";
		
		$result=$this->db->query($sql);
		
		if($result->num_rows){
			
			$intersect_key=array(
					"pl_id"=>1,
					"price"=>1,
					"discount"=>1,
					"discount_type"=>1,
					"tax_id"=>1,
					"search_words"=>1,
					"content"=>1,
					"short_desc"=>1,
					"title"=>1,
					"shipping"=>1,
	 				"model"=>1,
	 				"unique_code"=>1,
	 				"product_type_id"=>1,
	 				"last_update"=>1,
	 				"date_added"=>1
					
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
			
			foreach($result->result_array() as $l){
				
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product",$new_data);
			}
			
		}
	}

	function default_image(){
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
	
		$this->db->query("update product_image set default_img=0 where product_id=".$this->input->post("product_id"));
		$this->db->query("update product_image set default_img=1 where product_image_id=".$this->input->post("image_id"));
	
		echo 1;
		exit;
	
	}
	
		
}