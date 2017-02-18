<?php
class positional_content extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        

        $this->load->model('plugin_model');

        $this->load->model('positional_content_model');
        
        $this->load->library('page_position_lib');
        
        $this->data["plugin"]=$this->plugin=$this->plugin_model->plugin_by_key("positional_content_module")->row();
       
       $this->meta_type=6;
    }
 
    function get_shape(){
   
    return array(
    			
    			"positional_content_id" => $this->data["L"]["id"],
    			"title" => $this->data["L"]["title"],
    			"status" => $this->data["L"]["status"],
    			"pc_id"=> $this->data["L"]["id"],
				"language_id"=> $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_positional_content["language_id"])){
    		$this->db->where("language_id",$this->form_post_positional_content["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id", language_id());
    	}
		if(is_array($this->form_post_positional_content)){
		
			$this->form_post_positional_content_where=array_diff_key($this->form_post_positional_content,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"positional_content"=>"positional_content",
					"class_routes"=>"class_routes",
					"positional_content_category_id"=>"positional_content_category_id",
					"keywords"=>"keywords"
			));
			
			
			if(isset($this->form_post_positional_content["keywords"])){
			
				$this->db->or_like("lower(keywords)",strtolower($this->form_post_positional_content["keywords"]));
			}
			
			if(isset($this->form_post_positional_content["title"])){
					
				$this->db->or_like("lower(title)",strtolower($this->form_post_positional_content["title"]));
			}
			if(isset($this->form_post_positional_content["description"])){
					
				$this->db->or_like("lower(description)",strtolower($this->form_post_positional_content["description"]));
			}
			
			if(isset($this->form_post_positional_content["positional_content"])){
					
				$this->db->or_like("lower(positional_content)",strtolower($this->form_post_positional_content["positional_content"]));
			}
			
			if(isset($this->form_post_positional_content["positional_content_category_id"])){
			
				$positional_content_ids=$this->positional_content_model->positional_content_id_by_category_ids($this->form_post_positional_content["positional_content_category_id"]);
				if($positional_content_ids->num_rows >0 ){
					$this->db->where_in("pc_id",array_column($positional_content_ids->result_array(),"positional_content_id"));
				}else{
					$this->db->where_in("pc_id",0);
				}
			}
			
			$this->db->where($this->form_post_positional_content_where);
			
		}else{
		$this->form_post_positional_content=array();
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
		
			$this->form_post_positional_content=array_filter($this->input->post());
			
			if($this->input->post("status")==(-1)){
				$this->form_post_positional_content["status"]=0;
			}
			
			$this->sessiondd->set_userdata('form_post_positional_content',$this->form_post_positional_content);
		}else{
		
			$this->form_post_positional_content=$this->sessiondd->userdata('form_post_positional_content') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_positional_content;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('positional_content')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/positional_content/index';
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
		
		$positional_content_session=$this->sessiondd->userdata('positional_content');
		
		if (	isset($positional_content_session["orderby"])		){
		
		$this->db->order_by($positional_content_session["orderby"], $positional_content_session["orderby_order"]);
		}else{
		$this->db->order_by("positional_content_id", "desc");
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
 		
 		$positional_contents=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("positional_content");   ///segment 4 page
		
		$this->data["positional_contents"]=$this->modules->positional_content_list($positional_contents);
		
		if($action=="list"){
		echo $this->data["positional_contents"];
		exit;
		}
		
	
		
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content/positional_content',$this->data);
	 
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
		$this->form_validation->set_rules('content', $this->data["L"]["positional_content"], 'trim|required');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		//$this->data["POST"]=array_diff_key($this->input->post(),array("positional_content_category_id"=>"dd","plugin_to_page"=>"dd"));

	 		$this->data["POST"]["title"]=($this->data["POST"]["title"]);
	 		$this->data["POST"]["language_id"]=language_id();
	 		
	 		$this->db->insert("positional_content",$this->data["POST"]);
	 		$ids=$this->db->insert_id();

	 		$this->db->where("positional_content_id",$ids)->update("positional_content",array("pc_id"=>$ids));
	 		
	 		
 			///plugin dynamic to page ///
 			$this->page_position_lib->after_post(array("type_id"=>$ids,"plugin_id"=>$this->plugin->plugin_id));
 			///plugin dynamic to page ///
	 		
	 		$this->quick_model->logs($ids." id positional_content added !");
	 			
	 	redirect("admin/positional_content");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
			
		$this->page_position_lib->set_page_position_form(array("type_id"=>0,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/positional_content_form',$this->data);
	
    }
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("positional_content_id")>0){
    
    		$this->db->where("positional_content_id",$this->input->post("positional_content_id"))->update("positional_content",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }
    
    function edit($ids,$pc_id){
    
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
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=array_diff_key($this->input->post(),array("positional_content_category_id"=>"dd","plugin_to_page"=>"dd"));
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		$this->db->where("positional_content_id",$ids)->update("positional_content",$this->data["POST"]);
	 		
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>$pc_id,"plugin_id"=>$this->plugin->plugin_id));
	 		///plugin dynamic to page ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->db->where("positional_content_id",$ids)->get("positional_content")->row_array();
		
		$this->positional_content_language_create();

		$this->data["pc_group"]=$this->positional_content_model->pc_group($this->data["POST"]["pc_id"])->result_array();
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>$pc_id,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('content/positional_content_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			$this->db->where_in("pc_id",$this->input->post("positional_content_id"))->delete("positional_content");
			
			$this->quick_model->logs(implode(',',$this->input->post("positional_content_id"))." idli positional_contents deleted ! ");
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
		
			if($key=="positional_content_group"){
				$group=$this->db->where_in("positional_content_group_id",$value)->get("positional_content_group")->result_array();
				$message.= "<p>".implode(",",array_column($group,"title") )."</p>";
			}else if($key=="positional_content_category_id"){
				$group=$this->db->where_in("positional_content_category_id",$value)->get("positional_content_category")->result_array();
				$message.= "<p>".implode(",",array_column($group,"title") )."</p>";
			}else if($key=="positional_content_type_id"){
				
				$message.= "<p>positional_content Type : ".$this->data["positional_contentTypes"][$value]." </p>";
			}else{
				$message.= "<p>".$this->shape[$key]." : ".$value."</p>";
			}
			
			
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_positional_content',"");
	redirect('admin/positional_content');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$positional_content_session=$this->sessiondd->userdata('positional_content') ;
			
				$positional_content_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('positional_content',$positional_content_session);
			
				
				if(	isset($positional_content_session["orderby_order"]) ){
				
					if($positional_content_session["orderby_order"]=="asc" ){
					
						$positional_content_session["orderby_order"]="desc";
					}else{
					
						$positional_content_session["orderby_order"]="asc";
					}
					
				}else{
				
					$positional_content_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('positional_content',$positional_content_session);
			}
	
	redirect('admin/positional_content');
	}

	function positional_content_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join positional_content  as c
			on c.language_id=l.language_id and c.pc_id=".$this->data["POST"]["pc_id"]."
	
			where l.status=1 and ( c.positional_content_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"pc_id"=>1,
					"title"=>1,
				
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$new_data["title"].=" - ".$l["language_id"];
				$this->db->insert("positional_content",$new_data);
			}
	
		}
	}
	
		
}