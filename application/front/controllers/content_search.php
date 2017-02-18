<?php
class content_search extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
        $this->load->model('content_model');
    }
 
    function get_shape(){
   
    return array(
    			
    			"content_id" =>"",
    			"title" =>"",
    			"slug" => "",
				"short_desc" => "",
				"keywords" => "",
				"content_type_id" => "",
				"content_category_id" => "",
				"status"=> "",
				"c_id"=> "",
				"language_id"=> ""

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_content_search["language_id"])){
    		$this->db->where("language_id",$this->form_post_content_search["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id", language_id());
    	}
		if(is_array($this->form_post_content_search)){
		
			$this->form_post_content_search_where=array_diff_key($this->form_post_content_search,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"content"=>"content",
					"class_routes"=>"class_routes",
					"content_category_id"=>"content_category_id",
					"keywords"=>"keywords",
					"search_words"=>"search_words",
					"search_page"=>"search_page"
			));
			
			if(isset($this->form_post_content_search["search_words"])){
				
				$this->form_post_content_search["search_words"]=strip_tags($this->form_post_content_search["search_words"]);
				
				for ($i=0; $i<10; $i++ ){
					$this->form_post_content_search["search_words"]=str_replace("  "," ",$this->form_post_content_search["search_words"]);
				}
				
				$this->sessiondd->set_userdata("search_words",$this->form_post_content_search["search_words"]);
				$this->sessiondd->set_userdata("search_page",$this->form_post_content_search["search_page"]);
				
				
				foreach (explode(" ",$this->form_post_content_search["search_words"]) as $search_words){
					$this->db->where("(lower(content) like '%".strtolower($search_words)."%')");
				}
				
				$this->db->where("(lower(title) like '%".strtolower($this->form_post_content_search["search_words"])."%') or
						 (lower(keywords) like '%".strtolower($this->form_post_content_search["search_words"])."%') or
						(lower(description) like '%".strtolower($this->form_post_content_search["search_words"])."%')
						" );
				//$this->db->or_like("lower(content)",strtolower($this->form_post_content_search["search_words"]));
				
			}else{
				$this->sessiondd->set_userdata("search_words",null);
				$this->db->where("c_id",0);
			}
		
			$this->db->where($this->form_post_content_search_where);
			
		}else{
		$this->form_post_content_search=array();
		}	
		/// post1 *///	
		
		
    }
    
	function index($action=""){	

		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		
		$this->data["content_types"]=$this->content_model->content_types()->result_array();
		$this->data["contentTypes"]=array_column($this->data["content_types"],"title","content_type_id");
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_content_search=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_content_search',$this->form_post_content_search);
		}else{
		
			$this->form_post_content_search=$this->sessiondd->userdata('form_post_content_search') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_content_search;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('content')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=10;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/content_search/index';
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 3;
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
		
		$content_session=$this->sessiondd->userdata('content');
		
		if (	isset($content_session["orderby"])		){
		
		$this->db->order_by($content_session["orderby"], $content_session["orderby_order"]);
		}else{
		$this->db->order_by("content_id", "desc");
		}	
		///order by///
		
		 
		//// filter*  ////
		
		$page=$this->uri->segment(3);
		
		if(!empty($page) and $page!="list" ){
 		
 		$per_page_start=$per_page*($page-1);
 		}else{
 		$page=0;
 		$per_page_start=$per_page*$page;
 		}
 		
 		$contents=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("content");   ///segment 4 page
		
		$this->data["contents"]=$contents->result_array();
		
		$this->data["blog_link"]=$this->quick_model->get_link("/content/category");
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content_search',$this->data);
	 
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
			}else if($key=="serach_words"){
				
				$message.= $this->data["L"]["search"].": ".$value." </p>";
			}else{
				$message.= "<p>".$this->shape[$key]." : ".$value."</p>";
			}
			
			
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_content_search',"");
	redirect('admin/content');
	}

	
}