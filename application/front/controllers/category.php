<?php
class category extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('product_model');
        
       
    }
 
    function get_shape(){
   
    return array(
    			"product_id" =>$this->data["L"]["id"],
    			"image" => $this->data["L"]["image"],
    			"discount" => $this->data["L"]["discount"],
    			"color" =>$this->data["L"]["color"],
				"free" => $this->data["L"]["free"],
				"number" => $this->data["L"]["stock"],
    			"title" =>$this->data["L"]["title"],
    			"short_desc" => $this->data["L"]["description"],
				"slug" => $this->data["L"]["link"],
				"keywords" => $this->data["L"]["keywords"],
				"search_words" => $this->data["L"]["search"],
    			"price" => $this->data["L"]["price"],
				"category_id" => $this->data["L"]["category"],
				"status"=>$this->data["L"]["status"],
				"pl_id"=>$this->data["L"]["id"],
				"catalog"=>$this->data["L"]["ascatalog"]
    		
			);
    
    }
    
    function where_work(){
    	
    	if($this->category_id){
    		$this->form_post_product_f["category_id"]=array($this->category_id);
    	}
    	
    	$this->form_post_product_f["status"]=1;
    	$this->form_post_product_f["language_id"]=f_language_id();
    	
		if(is_array($this->form_post_product_f)){
		
			$this->form_post_product_f_where=array_diff_key($this->form_post_product_f,$pattern=array(
					"slider"=>"slider",
					"category_id"=>"Category",
					"keywords"=>"keywords",
					"search_words"=>"search_words",
					"search_page"=>"search_page",
					"description"=>"description",
					"title"=>"title",
					"product_feature"=>"Feature"
					));
			$this->db->where($this->form_post_product_f_where);
			
			
			
		if(isset($this->form_post_product_f["category_id"])){
				$product_ids=array(null);
				$sub=$this->db->query("select * from product_category where main_category_id in(".implode(",",$this->form_post_product_f["category_id"]).")");
				
				$categories=$this->form_post_product_f["category_id"];
				
				if($sub->num_rows){
					$categories=array_merge($categories,array_column($sub->result_array(),"cl_id"));
				}
				
				$pc=$this->db->query(" select * from product_to_category where category_id in (".implode(",",$categories).") "); 
				
				if($pc->num_rows){
					$product_ids=array_column($pc->result_array(),"product_id");
				}
					
				
					$this->db->where_in("pl_id",$product_ids);
				
			}

			if(isset($this->form_post_product_f["product_feature"])){
			
					
				$pc=$this->db->query(" select * from product_to_feature where selected='1' and  product_feature_id in (".implode(",",$this->form_post_product_f["product_feature"]).") group by `product_id`  having count(*) >= ".count($this->form_post_product_f["product_feature"])." ");
					
				if($pc->num_rows){
					$product_ids=array_column($pc->result_array(),"product_id");
					$this->db->where_in("pl_id",$product_ids);
				}
				
					
			}
		
			if(isset($this->form_post_product_f["slider"])){
				
				$price=explode(";",$this->form_post_product_f["slider"]);
				$this->db->where("price between ".$price[0]." AND ".$price[1]." ");
				
				$this->data["POST"]["price"]=$price[0]."$ - ".$price[1]."$";

				$this->data["price1"]=$price[0];
				$this->data["price2"]=$price[1];
				unset($this->data["POST"]["slider"]);
			}
			
			
			if(isset($this->form_post_product_f["search_words"])){
				
				$this->form_post_product_f["search_words"]=strip_tags($this->form_post_product_f["search_words"]);
				
				for ($i=0; $i<10; $i++ ){
					$this->form_post_product_f["search_words"]=str_replace("  "," ",$this->form_post_product_f["search_words"]);
				}
				
				$this->sessiondd->set_userdata("search_words",$this->form_post_product_f["search_words"]);
				$this->sessiondd->set_userdata("search_page",$this->form_post_product_f["search_page"]);

				//$this->db->like("lower(search_words)", strtolower($this->form_post_product_f["search_words"]));
				//$this->db->or_like("lower(title)",strtolower($this->form_post_product_f["search_words"]));
				//$this->db->or_like("lower(keywords)",strtolower($this->form_post_product_f["search_words"]));
				//$this->db->or_like("lower(content)",strtolower($this->form_post_product_f["search_words"]));
				//$this->db->or_like("(product_id)",($this->form_post_product_f["search_words"]));
				/*
				foreach (explode(" ",$this->form_post_product_f["search_words"]) as $search_words){
					$this->db->where("(lower(content) like '%".strtolower($search_words)."%')");
				}*/
				
				$this->db->where("(lower(title) like '%".strtolower($this->form_post_product_f["search_words"])."%') or
						(lower(content) like '%".strtolower($this->form_post_product_f["search_words"])."%') or
						(lower(keywords) like '%".strtolower($this->form_post_product_f["search_words"])."%') or
						(lower(description) like '%".strtolower($this->form_post_product_f["search_words"])."%')or
						(lower(product_id) like '%".strtolower($this->form_post_product_f["search_words"])."%') or
						(lower(model) like '%".strtolower($this->form_post_product_f["search_words"])."%') or
						(lower(search_words) like '%".strtolower($this->form_post_product_f["search_words"])."%')
						");
				
				
			}else{
				$this->sessiondd->set_userdata("search_words",null);
				
			}
				
				
			
		}else{
		$this->form_post_product_f=array();
		}	
		/// post1 *///	
    

		
    }
    
	function index($action=""){
		
		dbg($this->data["settings"]);
		$this->quick->Header("");

   		$this->shape=$this->get_shape();
   		
   		if(is_numeric($action) and $action>0){
   		
   			$this->category_id=$action;
   			$this->data["category_id"]=$this->category_id;
   			
   		}else{
   			$this->category_id=null;
   		}
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_product_f=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_product_f',$this->form_post_product_f);
		}else{
		
			$this->form_post_product_f=$this->sessiondd->userdata('form_post_product_f') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_f;

		
		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product')->num_rows();
		
		$per_page=$this->data["settings"]["product_perpage"];
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/category/index/page';
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 4;
		$config["num_links"] =6;// round($choice);
		//$config['cur_tag_open'] = '<b>';
		$config['last_link'] = $this->data["L"]["last"];
		$config['first_link'] =  $this->data["L"]["first"];
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
		
		$product_session=$this->sessiondd->userdata('product_front');
		$this->data["product_front"]=$product_session;

		//$this->dbg2($product_session);
		if (	isset($product_session["orderby"])		){
			
			if($product_session["orderby"]==1){
				
				$culonm="title";
				$incrising="asc";
			}elseif ($product_session["orderby"]==2){
				
				$culonm="title";
				$incrising="desc";
				
			}elseif ($product_session["orderby"]==3){
				
				$culonm="price";
				$incrising="asc";
				
			}elseif ($product_session["orderby"]==4){
				
				$culonm="price";
				$incrising="desc";
				
			}elseif ($product_session["orderby"]==5){
				
				$culonm="product_id";
				$incrising="desc";
			}else{
				$culonm="product_id";
				$incrising="desc";
			}
		
		$this->db->order_by($culonm, $incrising);
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
		
		if($products->num_rows and isset($this->form_post_product_f["category_id"])){
		$this->data["product_type_tree"]=$this->product_model->get_product_feature_tree(array_filter(array_column($products->result_array(),"pl_id")),f_language_id(),"1");
		}
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product',$this->data);
	 
	}

    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
		if($key=="product_feature"){
				$group=$this->db->where_in("f_id",$value)->where("language_id",f_language_id())->get("product_feature")->result_array();
				$message.= "<p><b>".implode(",",$this->quick->array_column($group,"feature_title") )."</b></p>";
			}elseif($key=="category_id"){
				$this->db->where("language_id",f_language_id());
				$group=$this->db->where_in("cl_id",$value)->get("product_category")->result_array();
				$message.= "<p><b>".$this->data["L"]["categories"]."</b> : ".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}elseif(in_array($key,array("language_id","status"))){
				
			}elseif($key=="search_page"){
				
			}else{
				$message.= "<p> <b>".ucwords($this->shape[$key])." </b> : ".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_f',"");
		
	redirect(base_url().$this->quick_model->get_link("category"));
	}


	function set_orderby(){
	
		if($this->input->post("orderby")){
	
			$product_session=$this->sessiondd->userdata('product_front') ;
				
			$product_session["orderby"]=$this->input->post("orderby");
	
			$this->sessiondd->set_userdata('product_front',$product_session);
				
		}
	
		redirect(base_url().$this->quick_model->get_link("/category"));
	}
	

	
	function feature($id){
		
		$this->form_post_product_f=$this->sessiondd->userdata('form_post_product_f') ;
		
		if(array_search($id,$this->form_post_product_f["product_feature"])){
			unset($this->form_post_product_f["product_feature"][$id]);
			if(count($this->form_post_product_f["product_feature"])==0){ unset($this->form_post_product_f["product_feature"]);}
			
		}else{
			$this->form_post_product_f["product_feature"][$id]=$id;
		}
		
		$this->sessiondd->set_userdata('form_post_product_f',$this->form_post_product_f);
	
		redirect(base_url().$this->quick_model->get_link("category"));
	}
	
}