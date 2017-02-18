<?php
class content_category extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('content_model');
       
       $this->meta_type=7;
    }
 
    function get_shape(){
   
    return array(
    			
    			"content_category_id" => $this->data["L"]["id"],
    			"sort_order" => $this->data["L"]["sorting"],
    			"title" => $this->data["L"]["title"],
    			"slug" => $this->data["L"]["link"],
				"description" => $this->data["L"]["description"],
				"keywords" => $this->data["L"]["keywords"],
				"content_type_id" =>$this->data["L"]["contenttype"],
				"status"=> $this->data["L"]["status"],
				"cc_id"=>$this->data["L"]["id"],
				"language_id"=>$this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_content_category["language_id"])){
    		$this->db->where("language_id",$this->form_post_content_category["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_content_category)){
		
			$this->form_post_content_category_where=array_diff_key($this->form_post_content_category,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"keywords"=>"keywords",
					"content"=>"content",
					"class_routes"=>"class_routes"
			));
			$this->db->where($this->form_post_content_category_where);
			
			
			if(isset($this->form_post_content_category["link"])){
				$this->db->like("link",$this->form_post_content_category["link"]);
			}
			
			if(isset($this->form_post_content_category["class_routes"])){
				$this->db->like("class_routes",$this->form_post_content_category["class_routes"]);
			}
			
			if(isset($this->form_post_content_category["keywords"])){
					
				$this->db->or_like("lower(keywords)",strtolower($this->form_post_content_category["keywords"]));
			}
				
			if(isset($this->form_post_content_category["title"])){
					
				$this->db->or_like("lower(title)",strtolower($this->form_post_content_category["title"]));
			
			}
			if(isset($this->form_post_content_category["description"])){
					
				$this->db->or_like("lower(description)",strtolower($this->form_post_content_category["description"]));
					
			}
				
			if(isset($this->form_post_content_category["content"])){
					
				$this->db->or_like("lower(content)",strtolower($this->form_post_content_category["content"]));
					
			}
			
		}else{
		$this->form_post_content_category=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){
		$this->permission->check_permission("view");

		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_content_category=array_filter($this->input->post());
			if($this->input->post("status")==(-1)){
				$this->form_post_content_category["status"]=0;
			}
			
			$this->sessiondd->set_userdata('form_post_content_category',$this->form_post_content_category);
		}else{
		
			$this->form_post_content_category=$this->sessiondd->userdata('form_post_content_category') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_content_category;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('content_category')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/content_category/index';
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 4;
		$config["num_links"] =6;// round($choice);
		//$config['cur_tag_open'] = '<b>';
		$config['last_link'] = 'Last';
		$config['first_link'] = "First";
		$config['prev_link'] = ' previous ';
		$config['next_link'] = ' next ';
		
		
		$config['full_tag_open'] = ' <div class="pagination pagination-small" style="text-align:left;"> <ul>';
		$config['full_tag_close'] = ' </ul></div>';
		
		$config['cur_tag_open'] = ' <li><a style="color:grey;font-weight: 600;">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = ' <li>';
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
		
		$content_category_session=$this->sessiondd->userdata('content_category');
		
		//$this->dbg2($content_category_session);
		if (	isset($content_category_session["orderby"])		){
		
		$this->db->order_by($content_category_session["orderby"], $content_category_session["orderby_order"]);
		}else{
		$this->db->order_by("content_category_id", "desc");
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
 		
 		$content_categorys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("content_category");   ///segment 4 page
		$this->data["content_categorys"]=$this->modules->content_category_list($content_categorys);
		
		if($action=="list"){
		echo $this->data["content_categorys"];
		exit;
		}
		
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
	
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content/content_category',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|xss_clean|callback_check_slug');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=array_diff_key($this->data["POST"],array("main_category_id"=>1));
	 		
	 		$this->data["POST"]["language_id"]=language_id();
	 		
	 		$this->db->insert("content_category",$this->data["POST"]);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		/// meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 		$meta_data["class_routes"]="/content/category/".$ids;
	 		$meta_data["title"]=$this->input->post("title");
	 		$meta_data["language_id"]=$this->data["POST"]["language_id"];
	 		$meta_data["type_l_id"]=$ids;
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"2");
	 		$this->db->where("content_category_id",$ids)->update("content_category",array("slug"=>$meta_data["link"],"cc_id"=>$ids));
	 		///meta
	 		
	 		
	 		$this->quick_model->logs($ids." id content_category added !");
	 		
	 		/// assign to main category ///
	 		$cc_id=$ids;
	 		if($this->input->post("main_category_id")){
	 			foreach($this->input->post("main_category_id") as $top_cc_id){
	 				if($top_cc_id!=$cc_id){
	 					$this->db->insert("ccategory_to_ccategory",array("top_cc_id"=>$top_cc_id,"sub_cc_id"=>$cc_id));
	 				}
	 			}
	 		}else{
	 			$this->db->insert("ccategory_to_ccategory",array("top_cc_id"=>0,"sub_cc_id"=>$cc_id));
	 		}
	 		/// assign to main category ///
	 		
	 	redirect("admin/content_category");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
		
		/// main category ///
		$this->data["categories"]=$this->content_model->category_tree();
		$this->data["lucky_category"]=array();
		/// main category ///
	
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_category_form',$this->data);
	
    }
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	//dbg($this->input->post());
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("content_category_id")>0){
    
    		$this->db->where("content_category_id",$this->input->post("content_category_id"))->update("content_category",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function edit($ids,$cc_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		
   		$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$content_type=$this->content_model->content_type($this->input->post("content_type_id"))->row_array();
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('slug', 'Slug', 'trim|xss_clean|callback_check_slug_edit');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		$this->data["POST"]=array_diff_key($this->data["POST"],array("main_category_id"=>1));
	 		
	 		$this->db->where("content_category_id",$ids)->update("content_category",$this->data["POST"]);
	 		
	 		$intersect_key=array(
	 				"only_link"=>1,
	 				"sort_order"=>1,
	 				"content_type_id"=>1
	 		);
	 		
	 		$cc_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 			count($cc_new_data) ? $this->db->where(array("cc_id"=>$cc_id,"language_id"=>$l["language_id"]))->update("content_category",$cc_new_data) : "";
	 		}
	 		
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta=$this->meta_model->meta($meta_data);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		$meta_data["language_id"]=$this->input->post("language_id");
	 		$meta_data["type_l_id"]=$cc_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			$meta_data["class_routes"]="/content/category/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"_".substr($this->quick->toAscii($this->data["POST"]["title"]),0,5) );
	 		
	 		$this->db->where("content_category_id",$ids)->update("content_category",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		
	 		/// assign to main category ///
	 		$this->db->where("sub_cc_id",$cc_id)->delete("ccategory_to_ccategory");
	 		if($this->input->post("main_category_id")){
	 			foreach($this->input->post("main_category_id") as $top_cc_id){
	 				if($top_cc_id!=$cc_id){
	 					$this->db->insert("ccategory_to_ccategory",array("top_cc_id"=>$top_cc_id,"sub_cc_id"=>$cc_id));
	 				}
	 			}
	 		}else{
	 			$this->db->insert("ccategory_to_ccategory",array("top_cc_id"=>0,"sub_cc_id"=>$cc_id));
	 		}
	 		/// assign to main category ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->content_model->content_category($ids)->row_array();
		
		$this->content_category_language_create();
		$this->data["cc_group"]=$this->content_model->cc_group($this->data["POST"]["cc_id"])->result_array();
		
		/// main category ///
		$this->data["categories"]=$this->content_model->category_tree();
    	$lucky_category=$this->content_model->lucky_category($this->data["POST"]["cc_id"]);
		if($lucky_category->num_rows){
			$this->data["lucky_category"]=array_column($lucky_category->result_array(),"top_cc_id");
		}else{
			$this->data["lucky_category"]=array();
		}
		/// main category ///
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_category_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			/// delete meta ///
			
			$this->db->where("type", $this->meta_type);
			$this->db->where_in("type_l_id",$this->input->post("content_category_id"))->delete("meta");
			
			
			/// delete meta ///
			
			$this->db->where_in("cc_id",$this->input->post("content_category_id"))->delete("content_category");
			
			$this->quick_model->logs(implode(',',$this->input->post("content_category_id"))." idli content_categorylar silindi ");
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
		
			if($key=="content_type_id"){
			
				$message.= "<p>".$this->shape[$key].": ".$this->content_model->content_type($value)->row()->title."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].": ".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_content_category',"");
	redirect('admin/content_category');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$content_category_session=$this->sessiondd->userdata('content_category') ;
			
				$content_category_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('content_category',$content_category_session);
			
				
				if(	isset($content_category_session["orderby_order"]) ){
				
					if($content_category_session["orderby_order"]=="asc" ){
					
						$content_category_session["orderby_order"]="desc";
					}else{
					
						$content_category_session["orderby_order"]="asc";
					}
					
				}else{
				
					$content_category_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('content_category',$content_category_session);
			}
	
	redirect('admin/content_category');
	}
	
	function sort_order(){
			
			
			if($this->input->is_ajax_request()){
			
				$category_ids=explode(',',$this->input->post("category_ids"));
					
					$i=1;
					foreach($category_ids as $category_id){
					
						$this->db->where("cc_id",$category_id)->update("content_category",array("sort_order"=>$i));
					
						$i++;
					}
				//$this->quick_model->logs("content category  sorted");
			exit;
			}
			
		$this->data["content_categorys"]=$this->content_model->content_categories_left()->result_array();
			
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_category_order',$this->data);
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

	function content_category_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join content_category as cc
			on cc.language_id=l.language_id and cc.cc_id=".$this->data["POST"]["cc_id"]."
	
			where l.status=1 and ( cc.content_category_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"cc_id"=>1,
					"content"=>1,
					"description"=>1,
					"keywords"=>1,
					"only_link"=>1,
					"title"=>1,
					"content_type_id"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("content_category",$new_data);
			}
	
		}
	}
	
	function dd(){
	
		dbg($this->content_model->category_tree());
	
	}
		
}