<?php
class content extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('content_model');
        $this->load->library('content_lib');

        $this->load->model('plugin_model');
        $this->load->library('page_position_lib');
        
        $this->data["plugin"]=$this->plugin=$this->plugin_model->plugin_by_key("content_module")->row();
       
       $this->meta_type=6;
    }
 
    function get_shape(){
   
    return array(
    			
    			"content_id" => $this->data["L"]["id"],
    			"title" => $this->data["L"]["title"],
    			"slug" =>  $this->data["L"]["link"],
				"short_desc" => $this->data["L"]["description"],
				"keywords" =>  $this->data["L"]["keywords"],
				"content_type_id" =>  $this->data["L"]["contenttype"],
				"content_category_id" =>  $this->data["L"]["category"],
				"status"=>  $this->data["L"]["status"],
				"c_id"=> $this->data["L"]["id"],
				"language_id"=> $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_content["language_id"])){
    		$this->db->where("language_id",$this->form_post_content["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id", language_id());
    	}
		if(is_array($this->form_post_content)){
		
			$this->form_post_content_where=array_diff_key($this->form_post_content,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"content"=>"content",
					"class_routes"=>"class_routes",
					"content_category_id"=>"content_category_id",
					"keywords"=>"keywords"
			));
			
			
			if(isset($this->form_post_content["keywords"])){
			
				$this->db->or_like("lower(keywords)",strtolower($this->form_post_content["keywords"]));
			}
			
			if(isset($this->form_post_content["title"])){
					
				$this->db->or_like("lower(title)",strtolower($this->form_post_content["title"]));
			}
			if(isset($this->form_post_content["description"])){
					
				$this->db->or_like("lower(description)",strtolower($this->form_post_content["description"]));
			}
			
			if(isset($this->form_post_content["content"])){
					
				$this->db->or_like("lower(content)",strtolower($this->form_post_content["content"]));
			}
			
			if(isset($this->form_post_content["content_category_id"])){
			
				$content_ids=$this->content_model->content_id_by_category_ids($this->form_post_content["content_category_id"]);
				if($content_ids->num_rows >0 ){
					$this->db->where_in("c_id",array_column($content_ids->result_array(),"content_id"));
				}else{
					$this->db->where_in("c_id",0);
				}
			}
			
			$this->db->where($this->form_post_content_where);
			
		}else{
		$this->form_post_content=array();
		}	
		/// post1 *///	
		
		
    }
    
	function index($action=""){	
		$this->permission->check_permission("view");

		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
		$this->data["contentTypes"]=array_column($this->data["content_types"],"title","content_type_id");
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_content=array_filter($this->input->post());
			
			if($this->input->post("status")==(-1)){
				$this->form_post_content["status"]=0;
			}
			
			$this->sessiondd->set_userdata('form_post_content',$this->form_post_content);
		}else{
		
			$this->form_post_content=$this->sessiondd->userdata('form_post_content') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_content;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('content')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/content/index';
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
		
		$content_session=$this->sessiondd->userdata('content');
		
		if (	isset($content_session["orderby"])		){
		
		$this->db->order_by($content_session["orderby"], $content_session["orderby_order"]);
		}else{
		$this->db->order_by("content_id", "desc");
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
 		
 		$contents=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("content");   ///segment 4 page
		
		$this->data["contents"]=$this->modules->content_list($contents);
		
		if($action=="list"){
		echo $this->data["contents"];
		exit;
		}
		
	
		if(	isset($this->data["POST"]["content_type_id"]) ){
		
		$checked=isset($this->data["POST"]["content_category_id"]) ? $this->data["POST"]["content_category_id"]: array();
		$this->data["content_categories"]=$this->content_lib->content_category_checkbox($this->data["POST"]["content_type_id"],$checked);
		}else{
		$this->data["content_categories"]=$this->content_lib->content_category_checkbox();
		}
		

		$this->data["categories"]=$this->content_model->category_tree();
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content/content',$this->data);
	 
	}

	function add(){
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->quick->Header("");
	$this->data["page"]="add";
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('title', $this->data["L"]["title"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('content', $this->data["L"]["content"], 'trim|required');
		$this->form_validation->set_rules('slug', $this->data["L"]["link"], 'trim|xss_clean|callback_check_slug');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=array_diff_key($this->input->post(),array("content_category_id"=>"dd","plugin_to_page"=>"dd"));
	 		$this->data["POST"]["date_added"]=mktime();
	 		$this->data["POST"]["title"]=($this->data["POST"]["title"]);
	 		$this->data["POST"]["language_id"]=language_id();
	 		
	 		$this->db->insert("content",$this->data["POST"]);
	 		$ids=$this->db->insert_id() ;
	 		
	 		foreach($this->input->post("content_category_id") as $content_category_id){
	 		
	 		$this->db->insert("content_to_category",array("content_id"=>$ids,"content_category_id"=>$content_category_id));
	 		}
	 		
	 		/// meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
 			$meta_data["class_routes"]="/content/view/".$ids;
 			$meta_data["title"]=$this->input->post("title");
 			$meta_data["language_id"]=$this->data["POST"]["language_id"];
 			$meta_data["type_l_id"]=$ids;
 		
 			$meta_data=$this->meta_model->insert_meta($meta_data,"_cntnt");
 			$this->db->where("content_id",$ids)->update("content",array("slug"=>$meta_data["link"],"c_id"=>$ids));
 			
 			
 			///plugin dynamic to page ///
 			$this->page_position_lib->after_post(array("type_id"=>$ids,"plugin_id"=>$this->plugin->plugin_id));
 			///plugin dynamic to page ///
	 		
	 		$this->quick_model->logs($ids." id content added !");
	 			
	 	redirect("admin/content");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["content_categories"]=$this->content_lib->content_category_checkbox();
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
		
		/// main category ///
		$this->data["categories"]=$this->content_model->category_tree();
		$this->data["lucky_category"]=array();
		
		/// main category ///
			
		$this->page_position_lib->set_page_position_form(array("type_id"=>0,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_form',$this->data);
	
    }
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("content_id")>0){
    
    		$this->db->where("content_id",$this->input->post("content_id"))->update("content",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }
    
    function edit($ids,$c_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    
	    $this->quick->Header("");
	    
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		
   	
	if( $this->input->post() and $this->permission->check_permission("edit") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title',$this->data["L"]["title"], 'trim|required|xss_clean');
		$this->form_validation->set_rules('content', $this->data["L"]["content"], 'trim|required');
		$this->form_validation->set_rules('slug',$this->data["L"]["link"], 'trim|xss_clean|callback_check_slug_edit');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=array_diff_key($this->input->post(),array("content_category_id"=>"dd","plugin_to_page"=>"dd"));
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		$this->db->where("content_id",$ids)->update("content",$this->data["POST"]);
	 		
	 		///
	 		$this->content_model->delete_content_category($c_id);
	 		if($this->input->post("content_category_id")){
		 		foreach($this->input->post("content_category_id") as $content_category_id){
		 		
		 		$this->db->insert("content_to_category",array("content_id"=>$c_id,"content_category_id"=>$content_category_id));
		 		}
	 		}
	 		///
	 		
	 		$intersect_key=array(
	 				"content_type_id"=>1,
	 				"related_id"=>1
	 		);
	 		
	 		$c_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 			count($c_new_data) ? $this->db->where(array("c_id"=>$c_id,"language_id"=>$l["language_id"]))->update("content",$c_new_data) : "";
	 		}
	 		
	 		/// meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta=$this->meta_model->meta($meta_data);
	 		
	 		$meta_data["title"]=$this->input->post("title");
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		$meta_data["language_id"]=$this->input->post("language_id");
			$meta_data["type_l_id"]=$c_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			
	 			$meta_data["class_routes"]="/content/view/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"_cntnt");
	 		$this->db->where("content_id",$ids)->update("content",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>$c_id,"plugin_id"=>$this->plugin->plugin_id));
	 		///plugin dynamic to page ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->content_model->content($ids)->row_array();
		
		$this->content_language_create();
		$this->data["c_group"]=$this->content_model->c_group($this->data["POST"]["c_id"])->result_array();
		
		$content_category_ids=$this->content_model->content_category_ids($c_id);
		$checked= $content_category_ids->num_rows >0 ? array_column($content_category_ids->result_array(),"content_category_id") : array();
		
		$this->data["content_categories"]=$this->content_lib->content_category_checkbox($this->data["POST"]["content_type_id"],$checked);
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
		
		/// main category ///
		$this->data["categories"]=$this->content_model->category_tree();
		$lucky_category=$this->content_model->content_to_category($this->data["POST"]["c_id"]);
		if($lucky_category->num_rows){
			$this->data["lucky_category"]=array_column($lucky_category->result_array(),"content_category_id");
		}else{
			$this->data["lucky_category"]=array();
		}
		/// main category ///
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>$c_id,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			/// delete meta ///
			
			$this->db->where("type", $this->meta_type);
			$this->db->where_in("type_l_id",$this->input->post("content_id"))->delete("meta");
			/// delete meta ///
			$this->db->where_in("c_id",$this->input->post("content_id"))->delete("content");
			
			$this->content_model->delete_content_category($this->input->post("content_id"));
			
			$this->quick_model->logs(implode(',',$this->input->post("content_id"))." idli contents deleted ! ");
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
		
			if($key=="content_group"){
				$group=$this->db->where_in("content_group_id",$value)->get("content_group")->result_array();
				$message.= "<p>".implode(",",array_column($group,"title") )."</p>";
			}else if($key=="content_category_id"){
				$group=$this->db->where_in("content_category_id",$value)->get("content_category")->result_array();
				$message.= "<p>".implode(",",array_column($group,"title") )."</p>";
			}else if($key=="content_type_id"){
				
				$message.= "<p>Content Type : ".$this->data["contentTypes"][$value]." </p>";
			}else{
				$message.= "<p>".$this->shape[$key]." : ".$value."</p>";
			}
			
			
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_content',"");
	redirect('admin/content');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$content_session=$this->sessiondd->userdata('content') ;
			
				$content_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('content',$content_session);
			
				
				if(	isset($content_session["orderby_order"]) ){
				
					if($content_session["orderby_order"]=="asc" ){
					
						$content_session["orderby_order"]="desc";
					}else{
					
						$content_session["orderby_order"]="asc";
					}
					
				}else{
				
					$content_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('content',$content_session);
			}
	
	redirect('admin/content');
	}
	
	function upload($content_id){
	
		
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
		$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/content/".$content_id;
		
        if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755);
        }
		
        $config['upload_path'] ='./uploads/content/'.$content_id.'/';
       
		//$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		foreach($result["success"] as $image){
			
			$image_data=array(
			"image_loc"=>'uploads/content/'.$content_id.'/'.$image["file_name"],
			"content_id"=>$content_id,
			"title"=>str_replace($image["file_ext"],"",$image["client_name"]),
			"file_ext"=>substr($image["file_ext"],1),
			"date_added"=>mktime()
			);
			$this->db->insert("content_image",	$image_data	);
			
		}
		
		}
    }
	
	function content_category_checkbox(){
	
		if($this->input->post("content_id")>0){
	
			$content_category_ids=$this->content_model->content_category_ids($this->input->post("content_id"));
			$checked= $content_category_ids->num_rows >0 ? array_column($content_category_ids->result_array(),"content_category_id") : array();
		}else{
		$checked=array();
		}	
	
	echo $this->content_lib->content_category_checkbox($this->input->post("content_type_id"),$checked); exit;
	
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
	
	function content_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join content  as c
			on c.language_id=l.language_id and c.c_id=".$this->data["POST"]["c_id"]."
	
			where l.status=1 and ( c.content_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"c_id"=>1,
					"content"=>1,
					"description"=>1,
					"keywords"=>1,
					"title"=>1,
					"content_type_id"=>1,
	 				"related_id"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("content",$new_data);
			}
	
		}
	}
	
		
}