<?php
class content_type extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('content_model');
        
        $this->meta_type=8;
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"content_type_id" =>$this->data["L"]["id"],
    			"sort_order" => $this->data["L"]["sorting"],
    			"title" => $this->data["L"]["title"],
    			"slug" => $this->data["L"]["link"],
				"description" => $this->data["L"]["description"],
				"keywords" => $this->data["L"]["keywords"],
				"status"=>$this->data["L"]["status"],
				"ct_id"=>$this->data["L"]["id"],
				"language_id"=>$this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_content_type["language_id"])){
    		$this->db->where("language_id",$this->form_post_content_type["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}	
		
		if(is_array($this->form_post_content_type)){
		
			$this->form_post_content_type_where=array_diff_key($this->form_post_content_type,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"keywords"=>"keywords",
					"content"=>"content",
					"class_routes"=>"class_routes"
			));
			$this->db->where($this->form_post_content_type_where);
			
			
			if(isset($this->form_post_content_type["link"])){
				$this->db->like("link",$this->form_post_content_type["link"]);
			}
			
			if(isset($this->form_post_content_type["class_routes"])){
				$this->db->like("class_routes",$this->form_post_content_type["class_routes"]);
			}
			
			if(isset($this->form_post_content_type["keywords"])){
					
				$this->db->or_like("lower(keywords)",strtolower($this->form_post_content_type["keywords"]));
			}
				
			if(isset($this->form_post_content_type["title"])){
					
				$this->db->or_like("lower(title)",strtolower($this->form_post_content_type["title"]));
			
			}
			if(isset($this->form_post_content_type["description"])){
					
				$this->db->or_like("lower(description)",strtolower($this->form_post_content_type["description"]));
					
			}
				
			if(isset($this->form_post_content_type["content"])){
					
				$this->db->or_like("lower(content)",strtolower($this->form_post_content_type["content"]));
					
			}
			
			
		}else{
		$this->form_post_content_type=array();
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
		
			$this->form_post_content_type=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_content_type',$this->form_post_content_type);
		}else{
		
			$this->form_post_content_type=$this->sessiondd->userdata('form_post_content_type') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_content_type;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('content_type')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/content_type/index';
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
		
		$content_type_session=$this->sessiondd->userdata('content_type');
		
		//$this->dbg2($content_type_session);
		if (	isset($content_type_session["orderby"])		){
		
		$this->db->order_by($content_type_session["orderby"], $content_type_session["orderby_order"]);
		}else{
		$this->db->order_by("content_type_id", "desc");
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
 		
 		$content_types=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("content_type");   ///segment 4 page
		$this->data["content_types"]=$this->modules->content_type_list($content_types);
		
		if($action=="list"){
		echo $this->data["content_types"];
		exit;
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content/content_type',$this->data);
	 
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
	 		
	 		$data_content_type=$this->input->post();
	 		
	 		$data_content_type["language_id"]=language_id();
	 		
	 		$this->db->insert("content_type",$data_content_type);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		/// meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			$meta_data["class_routes"]="/content/index/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 			$meta_data["language_id"]=$data_content_type["language_id"];
	 			$meta_data["type_l_id"]=$ids;
	 		
	 			$meta_data=$this->meta_model->insert_meta($meta_data,"2");
	 			$this->db->where("content_type_id",$ids)->update("content_type",array("slug"=>$meta_data["link"],"ct_id"=>$ids));
	 		///meta
	 		
	 		$this->quick_model->logs($ids." idli content_type added !");
	 	
	 		
	 	redirect("admin/content_type");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	 }
	
		
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_type_form',$this->data);
	
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
    
    	if($this->input->post("content_type_id")>0){
    
    		$this->db->where("content_type_id",$this->input->post("content_type_id"))->update("content_type",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function edit($ids,$ct_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
	    
	    $this->ids=$ids;
	    $this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
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
	 		
	 		$this->db->where("content_type_id",$ids)->update("content_type",$this->data["POST"]);
	 		
	 		$intersect_key=array(
	 				"only_link"=>1
	 		);
	 		
	 		$ct_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 			count($ct_new_data) ? $this->db->where(array("ct_id"=>$ct_id,"language_id"=>$l["language_id"]))->update("content_type",$ct_new_data) : "";
	 		}
	 		
	 		/// meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta=$this->meta_model->meta($meta_data);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		$meta_data["language_id"]=$this->input->post("language_id");
	 		$meta_data["type_l_id"]=$ct_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			
	 			$meta_data["class_routes"]="/content/index/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"2");
	 		$this->db->where("content_type_id",$ids)->update("content_type",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		$this->quick->success[]="Başarıyla editlendi";
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->content_model->content_type($ids)->row_array();
		$this->content_type_language_create();
		
		$this->data["ct_group"]=$this->content_model->ct_group($this->data["POST"]["ct_id"])->result_array();
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_type_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			foreach ($this->input->post("content_type_id") as $ct_id){
				$this->data["ct_group"]=$this->content_model->ct_group($ct_id)->result_array();
				
				foreach($this->data["ct_group"] as $row){
				/// delete meta ///
				$this->db->where("type", $this->meta_type);
				$this->db->where_in("type_id",$row["content_type_id"])->delete("meta");
				/// delete meta ///
				}
			
			}
			$this->db->where_in("ct_id",$this->input->post("content_type_id"))->delete("content_type");
			
			$this->quick_model->logs(implode(',',$this->input->post("content_type_id"))." idli content_typelar silindi ");
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
		
			if($key=="content_type_group"){
				$group=$this->db->where_in("content_type_group_id",$value)->get("content_type_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_content_type',"");
	redirect('admin/content_type');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$content_type_session=$this->sessiondd->userdata('content_type') ;
			
				$content_type_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('content_type',$content_type_session);
			
				
				if(	isset($content_type_session["orderby_order"]) ){
				
					if($content_type_session["orderby_order"]=="asc" ){
					
						$content_type_session["orderby_order"]="desc";
					}else{
					
						$content_type_session["orderby_order"]="asc";
					}
					
				}else{
				
					$content_type_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('content_type',$content_type_session);
			}
	
	redirect('admin/content_type');
	}
	
	function sort_order(){
			
			
			if($this->input->is_ajax_request()){
			
				$type_ids=explode(',',$this->input->post("type_ids"));
					
					$i=1;
					foreach($type_ids as $type_id){
					
						$this->db->where("ct_id",$type_id)->update("content_type",array("sort_order"=>$i));
					
						$i++;
					}
			//	$this->quick_model->logs("content type sorted");
			exit;
			}
			
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
			
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/content_type_order',$this->data);
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
	
	function content_type_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join content_type as ct
			on ct.language_id=l.language_id and ct.ct_id=".$this->data["POST"]["ct_id"]."
		
			where l.status=1 and ( ct.content_type_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
				
			$intersect_key=array(
					"ct_id"=>1,
					"content"=>1,
					"description"=>1,
					"keywords"=>1,
					"sort_order"=>1,
					"only_link"=>1,
					"title"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
				
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("content_type",$new_data);
			}
				
		}
	}
	
	
		
}