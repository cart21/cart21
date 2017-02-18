<?php
class content extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('content_model');
        $this->load->library('content_lib');

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
				"language_id"=> $this->data["L"]["language"],
				"date_added"=> $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	$this->db->where("language_id", f_language_id());
    	$this->db->where("status",1);
		if(is_array($this->form_post_content_f)){
		
			$this->form_post_content_f_where=array_diff_key($this->form_post_content_f,$pattern=array(
					"link"=>"link",
					"title"=>"title",
					"description"=>"description",
					"content"=>"content",
					"class_routes"=>"class_routes",
					"content_category_id"=>"content_category_id",
					"keywords"=>"keywords"
			));
			
			
			if(isset($this->form_post_content_f["content_category_id"])){
			
				$content_ids= $this->db->query("select * from content_to_category where content_category_id  in (".implode(",",$this->form_post_content_f["content_category_id"]).")");
				
				if($content_ids->num_rows >0 ){
				$this->db->where_in("c_id",array_column($content_ids->result_array(),"content_id"));
				}else{
					$this->db->where_in("c_id",0);
				}
			}
			
			$this->db->where($this->form_post_content_f_where);
			
		}else{
		$this->form_post_content_f=array();
		}	
		/// post1 *///	
		
		
    }
    
	function category($id=0){	
		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_content_f=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_content_f',$this->form_post_content_f);
		}else{
		
			
			$this->form_post_content_f=$this->sessiondd->userdata('form_post_content_f') ;
		}
		
		if(is_numeric($id) and $id>0){

			$this->data["content_category"]=$this->content_model->content_category($id)->row_array();
			
			$this->form_post_content_f["content_category_id"][]=$this->data["content_category"]["cc_id"];
				
		}else{
			$this->data["content_category"]["title"]="";
			$this->data["content_category"]["only_link"]="";
		}
		
		$this->where_work();

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('content')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=$this->data["settings"]["content_perpage"];
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/content.html/'.$id;
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 3;
		$config["num_links"] =6;
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
 		
 		$contents=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("content");   ///segment 3 page
	
		$this->data["contents"]=$contents->result_array();//$this->modules->content_list($contents);
		
		$this->data["blog_link"]=$this->quick_model->get_link("/content/category");
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
	$this->smarty->view('content/content_category',$this->data);
	 
	}
	
	function view($id) {
	
		$contents=$this->content_model->content($id);
		 
		$this->data["content_detail"]=$contents->num_rows>0 ?$contents->row_array() :0 ;
		
		$this->data["breadcrumb_categories"]=$this->content_model->content_category_left($this->data["content_detail"]["c_id"])->result_array();
		$this->data["blog_link"]=$this->quick_model->get_link("/content/category");

		$this->redirect_language();
	
		$r=array("[PHP]"=>"<pre>","[/PHP]"=>"</pre>");
		$this->data["content_detail"]["content"]=strtr($this->data["content_detail"]["content"],$r);

		
		$this->data["related_contents"]=$this->content_model->related_contents(explode(",",$this->data["content_detail"]["related_id"]))->result_array();
		
		$this->data["blog_link"]=$this->quick_model->get_link("/content/category");
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
		$this->smarty->view('content/content_view',$this->data);
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
	
		$this->sessiondd->set_userdata('form_post_content_f',"");
	redirect('/content.html');
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

	function redirect_language(){
	
		if($this->data["content_detail"]["language_id"]!=f_language_id()){
			 
			$redirect=$this->content_model->content_opt(array(
					"language_id"=>f_language_id(),
					"c_id"=>$this->data["content"]["c_id"]
			));
			if($redirect->num_rows ){
				redirect(base_url().$redirect->row()->slug);
				exit;
			}
			else{
				redirect("404");
				exit;
			}
		}
	
	}
		
}