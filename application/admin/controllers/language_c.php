<?php
class language_c extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        
        $this->languages=$this->language_model->languages();
    }
 
    function get_shape(){
   
    return array(
    			
    			"language_c_id" => $this->data["L"]["id"],
    			"key_val" => $this->data["L"]["lckeyval"],
    			"text_val" =>$this->data["L"]["lctextval"],
				"language_id" => $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
			$this->db->where_in("language_id",array_column($this->languages->result_array(),"language_id") );
		
		if(is_array($this->form_post_language_c)){
		
			$this->form_post_language_c_where=array_diff_key($this->form_post_language_c,$pattern=array(
					"key_val"=>"key_val",
					"text_val"=>"text_val",
					"meta_id"=>"meta_id",
					"section_id"=>"section_id",
					"language_id"=>"language_id",
					"download_as_exel"=>"download_as_exel"
			));
			$this->db->where($this->form_post_language_c_where);
			
			
			if(isset($this->form_post_language_c["key_val"])){
				$this->db->like("lower(key_val)",strtolower($this->form_post_language_c["key_val"]));
			}
			
			if(isset($this->form_post_language_c["text_val"])){
				$this->db->where("(lower(text_val) like '%".strtolower($this->form_post_language_c["text_val"])."%' or lower(key_val) like '%".strtolower($this->form_post_language_c["text_val"])."%')" );
			}

			if(isset($this->form_post_language_c["meta_id"])){
				$language_c_ids=$this->db->query("select * from language_c_to_page where meta_id in(".implode(",",$this->form_post_language_c["meta_id"]).")");
				if($language_c_ids->num_rows){
				$this->db->where_in("language_c_id",array_column($language_c_ids->result_array(), "language_c_id") );
				}else{
					$this->db->where_in("language_c_id",array(0) );
				}
				
			}
			
			if(isset($this->form_post_language_c["section_id"])){
				$language_c_ids=$this->db->query("select * from language_c_to_section where section_id in(".implode(",",$this->form_post_language_c["section_id"]).")");
				if($language_c_ids->num_rows){
				$this->db->where_in("language_c_id",array_column($language_c_ids->result_array(), "language_c_id") );
				}else{
					$this->db->where_in("language_c_id",array(0) );
				}
			}	
			
			if(isset($this->form_post_language_c["language_id"])){
				$this->db->where_in("language_id",$this->form_post_language_c["language_id"]);
			}
			
		}else{
		$this->form_post_language_c=array();
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
		
			$this->form_post_language_c=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_language_c',$this->form_post_language_c);
		}else{
		
			$this->form_post_language_c=$this->sessiondd->userdata('form_post_language_c') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_language_c;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('language_c')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/language_c/index';
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
		
		$config['cur_tag_open'] = ' <li><a href="#" class="bg-navy text-white">1 <span class="sr-only">';
		$config['cur_tag_close'] = '</span></a></li>';
		
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
		
		$language_c_session=$this->sessiondd->userdata('language_c');
		
		//$this->dbg2($language_c_session);
		if (	isset($language_c_session["orderby"])		){
		
		$this->db->order_by($language_c_session["orderby"], $language_c_session["orderby_order"]);
		}else{
		$this->db->order_by("language_c_id", "desc");
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
 		
 		$language_cs=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("language_c");   ///segment 4 page
	
		$this->data["language_cs"]=$this->modules->language_c_list($this->smarty,$language_cs);
		
		if($action=="list"){
		echo $this->data["language_cs"];
		exit;
		}
		
	

		$this->data["language_page"]=$this->language_model->language_page()->result_array();
		$this->data["permission_sections"]=$this->language_model->permission_sections()->result_array();
		
		$this->data["languages"]=$this->languages->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('language/language_c',$this->data);
	 
	}
	
	function download_as_exel(){
		
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
				
		$this->load->library("excel");
		$this->load->library("language_lib");
		
		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_language_c=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_language_c',$this->form_post_language_c);
		}else{
		
			$this->form_post_language_c=$this->sessiondd->userdata('form_post_language_c') ;
		}
		$this->where_work();
		$this->language_lib->download($this->db->get('language_c')->result_array());
			
		
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
		
		$this->data["POST"]=$this->input->post();
		if( $this->input->post() and $this->permission->check_permission("add") ){
		
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
		    
		    
			$this->form_validation->set_rules('text_val', 'Text', 'trim|required|xss_clean');	
			$this->form_validation->set_rules('key_val', 'Key', 'trim|required|xss_clean|callback_check_key');	
			
		 	if ($this->form_validation->run() 	) { 
		 		
		 		$data=array_diff_key($this->data["POST"],array("meta_id"=>1,"section_id"=>1));
		 		foreach ($this->languages->result_array() as $language ){
		 			
		 			$data["language_id"]=$language["language_id"];
		 			
		 			$check_lang=$this->language_model->language_c(array("language_id"=>$language["language_id"],"key_val"=>$this->data["POST"]["key_val"]));
		 			
		 			if($check_lang->num_rows==0){
			 		$this->db->insert("language_c",$data);
			 		
			 		$ids=$this->db->insert_id() ;
			 		
			 		/// page ///
			 		if($this->input->post("meta_id")){
			 		
			 			foreach($this->input->post("meta_id") as $m){
			 					
			 				$this->db->insert("language_c_to_page",array("language_c_id"=>$ids,"meta_id"=>$m));
			 			}
			 		}
			 		/// page ///
			 		/// section ///
			 		if($this->input->post("section_id")){
			 		
			 			foreach($this->input->post("section_id") as $m){
			 					
			 				$this->db->insert("language_c_to_section",array("language_c_id"=>$ids,"section_id"=>$m));
			 			}
			 		}
			 		/// sections ///
		 			}
		 		
		 		}
		 		
		 		$this->quick_model->logs($ids." id language_c added  ");
		 		
		 	redirect("admin/language_c");
		 	}else {
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
			}
		}
		
		$this->data["language_page"]=$this->language_model->language_page()->result_array();
		$this->data["permission_sections"]=$this->language_model->permission_sections()->result_array();
		
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('language/language_c_form',$this->data);
	
    }
    
    function edit($ids){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		
   		$this->data["POST"]=$this->language_model->language_c(array("language_c_id"=>$ids))->row_array();
   	
	$this->data["language_group"]=$this->language_model->language_c_group($this->data["POST"]["key_val"])->result_array();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->data["POST"]=$this->input->post();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('text_val', 'Text', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('key_val', 'Key', 'trim|required|xss_clean|callback_check_key_edit');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data=array_diff_key($this->data["POST"],array("meta_id"=>1,"section_id"=>1));
	 		
	 		$this->db->where("language_c_id",$ids)->update("language_c",$data);
	 		
	 		foreach ($this->data["language_group"] as $language){
	 			
	 			$this->db->where("language_c_id",$language["language_c_id"])->update("language_c",array("key_val"=>$this->data["POST"]["key_val"]));
		 		/// page ///
		 		$this->language_model->delete_language_c_page($language["language_c_id"]);
		 		
		 		if($this->input->post("meta_id")){
		 			foreach($this->input->post("meta_id") as $m){
		 					
		 				$this->db->insert("language_c_to_page",array("language_c_id"=>$language["language_c_id"],"meta_id"=>$m));
		 			}
		 		}
		 		/// page ///
		 		
		 		/// section ///
		 		$this->language_model->delete_language_c_section($language["language_c_id"]);
		 		 
		 		if($this->input->post("section_id")){
		 			foreach($this->input->post("section_id") as $m){
		 		
		 				$this->db->insert("language_c_to_section",array("language_c_id"=>$language["language_c_id"],"section_id"=>$m));
		 			}
		 		}
		 		/// section ///
	 		}
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 	redirect("admin/language_c");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["language_page"]=$this->language_model->language_page()->result_array();
		$this->data["permission_sections"]=$this->language_model->permission_sections()->result_array();
		
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->data["POST"]=$this->language_model->language_c(array("language_c_id"=>$ids))->row_array();
		
		$lucy_page=$this->language_model->language_to_page($ids);
		if($lucy_page->num_rows){
			$this->data["POST"]["meta_id"]=array_column($lucy_page->result_array(),"meta_id");
		}else{
			$this->data["POST"]["meta_id"]=array();
		}
		
		$lucy_section=$this->language_model->language_to_section($ids);
		if($lucy_section->num_rows){
			$this->data["POST"]["section_id"]=array_column($lucy_section->result_array(),"section_id");
		}else{
			$this->data["POST"]["section_id"]=array();
		}
		
		
		
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('language/language_c_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			dbg2($this->input->post("language_c_id"));
			$this->db->where_in("language_c_id",$this->input->post("language_c_id"))->delete("language_c");
			$this->quick_model->logs(implode(',',$this->input->post("language_c_id"))." idli language constant deleted ");
			$result="1";
		}else{
			$result="0";
		}
	   	echo $result;
    	exit;	
    }
    
    function language_c_link_order(){
    
    	if($this->input->is_ajax_request()){
    		 
    		$type_ids=explode(',',$this->input->post("type_ids"));
    
    		$i=1;
    		foreach($type_ids as $type_id){
    
    			$this->db->where("language_c_id",$type_id)->update("language_c",array($this->input->post("sort_order")=>$i));
    
    			$i++;
    		}
    		$this->quick_model->logs($this->input->post("sort_order")." link sorted");
    		exit;
    	}
    
    	$this->data["footers"]=$this->quick_model->footer_link()->result_array();
    	$this->data["top_menus"]=$this->quick_model->top_link()->result_array();
    	 
    	 
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('language/language_c_order',$this->data);
    }
    
 
    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="meta_id"){
				$group=$this->db->where_in("meta_id",$value)->get("meta")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}elseif($key=="language_id"){
				$group=$this->db->where_in("language_id",$value)->get("language")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"name") )."</p>";
			}elseif($key=="meta_id"){
				$group=$this->db->where_in("m_id",$value)->get("meta")->result_array();
				$message.= "<p>".implode(",",array_column($group,"title") )."</p>";
			}elseif($key=="section_id"){
				$group=$this->db->where_in("section_id",$value)->get("permission_sections")->result_array();
				$message.= "<p>".implode(",",array_column($group,"name") )."</p>";
			}elseif(in_array($key,array("download_as_exel"))){
			
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_language_c',"");
	redirect('admin/language_c');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$language_c_session=$this->sessiondd->userdata('language_c') ;
			
				$language_c_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('language_c',$language_c_session);
			
				
				if(	isset($language_c_session["orderby_order"]) ){
				
					if($language_c_session["orderby_order"]=="asc" ){
					
						$language_c_session["orderby_order"]="desc";
					}else{
					
						$language_c_session["orderby_order"]="asc";
					}
					
				}else{
				
					$language_c_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('language_c',$language_c_session);
			}
	
	redirect('admin/language_c');
	}
	
	function check_key($key){

    	$language_c=$this->language_model->language_c(array("key_val"=>$key,"language_id"=>$this->data["POST"]["language_id"]));
    	
    	if($language_c->num_rows>0){
    		$this->form_validation->set_message('check_key', 'The Constant '.$language_c->row()->key_val.' %s is already in use in language id:'.$language_c->row()->language_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}
	
	function check_key_edit($key){
		
		$this->db->where("language_c_id <>",$this->ids);
		$this->db->where("language_id",$this->data["POST"]["language_id"]);
    	$language_c=$this->language_model->language_c(array("key_val"=>$key));
    	
    	if($language_c->num_rows>0){
    		$this->form_validation->set_message('check_key_edit', 'The Constant '.$language_c->row()->key_val.' %s is already in use in language id:'.$language_c->row()->language_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}
	

	
	
		
}