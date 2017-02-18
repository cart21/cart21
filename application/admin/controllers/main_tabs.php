<?php
class main_tabs extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('tabs_model');
        $this->load->model('product_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"main_tabs_id" => $this->data["L"]["id"],
    			"sort_order" => $this->data["L"]["sorting"],
    			"status" => $this->data["L"]["status"],
    			"title" => $this->data["L"]["title"]

			);
    
    }
    
    function where_work(){
    
		if(is_array($this->form_post_main_tabs)){
		
			$this->form_post_main_tabs_where=array_diff_key($this->form_post_main_tabs,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_main_tabs_where);
			
			
			if(isset($this->form_post_main_tabs["link"])){
				$this->db->like("link",$this->form_post_main_tabs["link"]);
			}
			
			if(isset($this->form_post_main_tabs["class_routes"])){
				$this->db->like("class_routes",$this->form_post_main_tabs["class_routes"]);
			}
			
		}else{
		$this->form_post_main_tabs=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){
	
		$this->permission->check_permission("view");
		$this->quick->Header("");
		$this->quick->Top_menu("");
	
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_main_tabs=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_main_tabs',$this->form_post_main_tabs);
		}else{
		
			$this->form_post_main_tabs=$this->sessiondd->userdata('form_post_main_tabs') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_main_tabs;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('main_tabs')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/main_tabs/index';
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
		
		$main_tabs_session=$this->sessiondd->userdata('main_tabs');
		
		//$this->dbg2($main_tabs_session);
		if (	isset($main_tabs_session["orderby"])		){
		
		$this->db->order_by($main_tabs_session["orderby"], $main_tabs_session["orderby_order"]);
		}else{
		$this->db->order_by("sort_order", "asc");
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
 		
 		
 		
 		
 		$main_tabss=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("main_tabs");   ///segment 4 page
		$this->data["main_tabss"]=$this->modules->main_tabs_list($main_tabss);
		
		if($action=="list"){
		echo $this->data["main_tabss"];
		exit;
		}
		
		
		
		$this->quick->Footer("");
		
			
	$this->smarty->view('main_tabs/main_tabs',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	$this->quick->Header("");
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');		
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data_main_tabs=$this->data["POST"];
	 		
	 		$this->db->insert("main_tabs",$data_main_tabs);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		$key_id=$this->language_model->insert(array("key_val"=>"maintab".$ids,"text_val"=>$this->data["POST"]["title"]));
	 		
	 		foreach ($key_id as $id ){
	 			
	 			$this->db->insert("language_c_to_section",array("section_id"=>29,"language_c_id"=>$id));
	 			$this->db->insert("language_c_to_page",array("meta_id"=>9184,"language_c_id"=>$id));
	 				
	 		}
	 		
	 		$this->quick_model->logs($ids." idli main_tabs added !");
	 	
	 		
	 	redirect("admin/main_tabs");
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
	$this->smarty->view('main_tabs/main_tabs_form',$this->data);
	
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
    
    	if($this->input->post("main_tabs_id")>0){
    
    		$this->db->where("main_tabs_id",$this->input->post("main_tabs_id"))->update("main_tabs",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }

    function view($ids){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->data["page"]="edit";

    	$this->quick->Header("");
    	 
    	$this->ids=$ids;
    	 
    	if( $this->input->post() and $this->permission->check_permission("add") ){
    
    		$this->data["POST"]=$this->input->post() ;
    		$this->load->helper(array('form', 'url'));
    		$this->load->library('form_validation');
    	  
    		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');
    
    		if ($this->form_validation->run() 	) {
    
    			$data_main_tabs=array_diff_key($this->data["POST"],array("title"=>1));
    			
    			$this->db->where("main_tabs_id",$ids)->update("main_tabs",$data_main_tabs);
    			
    			$this->language_model->update(array("key_val"=>"maintab".$ids,"text_val"=>$this->data["POST"]["title"]));
    			
    			$this->quick->success[]="Successfully Edited !";
    
    		}else {
    			$verrors=array_filter(explode('.',validation_errors()));
    			foreach($verrors as $verror){
    				$this->quick->errors[] = strip_tags($verror).".";
    			}
    		}
    	}
    
    	$this->data["POST"]=$this->tabs_model->main_tabs($ids)->row_array();
    	$this->data["tabs_products"]=$this->tabs_model->tabs_product($ids)->result_array();
    	$this->data["products_select"]=$this->product_model->products(array("status"=>1,"language_id"=>language_id()))->result_array();
    
    	$l_c_text=$this->language_model->language_c(array("key_val"=>"maintab".$ids,"language_id"=>$_SESSION["cart21_a_language"]["language_id"]));
    	$this->data["POST"]["title"]= $l_c_text->num_rows ?  $l_c_text->row()->text_val : "";
    
    	
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('main_tabs/main_tabs_view',$this->data);
    
    
    }

    function edit($ids){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->data["page"]="edit";
    	 
    	$this->ids=$ids;
    	 
    	if( $this->input->post() and $this->permission->check_permission("add") ){
    		
    		$this->data["POST"]=$this->input->post() ;
    		$this->load->helper(array('form', 'url'));
    		$this->load->library('form_validation');
    	  
    		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');
    
    		if ($this->form_validation->run() 	) {
    
    			$data_main_tabs=array_diff_key($this->data["POST"],array("title"=>1));
    			
    			//$this->db->where("main_tabs_id",$ids)->update("main_tabs",$data_main_tabs);
    			
    			$this->language_model->update(array("key_val"=>"maintab".$ids,"text_val"=>$this->data["POST"]["title"]));
    
    			$this->quick->success[]="Successfully Edited !";
    
    		}else {
    			$verrors=array_filter(explode('.',validation_errors()));
    			foreach($verrors as $verror){
    				$this->quick->errors[] = strip_tags($verror).".";
    			}
    		}
    	}
    	
    	
    	if($this->input->is_ajax_request()){ echo 1; exit;}
    
    	$this->data["POST"]=$this->tabs_model->main_tabs($ids)->row_array();
    	$this->data["tabs_products"]=$this->tabs_model->tabs_product($ids)->result_array();
    	$this->data["products_select"]=$this->product_model->products(array("status"=>1))->result_array();
    
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('main_tabs/main_tabs_form',$this->data);
    
    
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			$this->db->where_in("main_tabs_id",$this->input->post("main_tabs_id"))->delete("main_tabs");
			$this->db->where_in("main_tabs_id",$this->input->post("main_tabs_id"))->delete("main_tabs_to_product");
			
			foreach ($this->input->post("main_tabs_id") as $main_tabs_id){
			$this->db->where_in("key_val","maintab".$main_tabs_id)->delete("language_c");
			}
			
			$this->quick_model->logs(implode(',',$this->input->post("main_tabs_id"))." idli main_tabslar deleted ");
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
		
			if($key=="main_tabs_group"){
				$group=$this->db->where_in("main_tabs_group_id",$value)->get("main_tabs_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_main_tabs',"");
	redirect('admin/main_tabs');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$main_tabs_session=$this->sessiondd->userdata('main_tabs') ;
			
				$main_tabs_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('main_tabs',$main_tabs_session);
			
				
				if(	isset($main_tabs_session["orderby_order"]) ){
				
					if($main_tabs_session["orderby_order"]=="asc" ){
					
						$main_tabs_session["orderby_order"]="desc";
					}else{
					
						$main_tabs_session["orderby_order"]="asc";
					}
					
				}else{
				
					$main_tabs_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('main_tabs',$main_tabs_session);
			}
	
	redirect('admin/main_tabs');
	}
	
	function sort_order(){
			
			if($this->input->is_ajax_request()){
			
				$type_ids=explode(',',$this->input->post("type_ids"));
					
					$i=1;
					foreach($type_ids as $type_id){
					
						$this->db->where("main_tabs_id",$type_id)->update("main_tabs",array("sort_order"=>$i));
					
						$i++;
					}
				$this->quick_model->logs("content type sorted");
			exit;
			}
			
		$this->data["main_tabss"]=$this->tabs_model->tabs()->result_array();
			
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('main_tabs/main_tabs_order',$this->data);
	}
	
	function tabs_product_sort_order(){
			
			if($this->input->is_ajax_request()){
			
				$type_ids=explode(',',$this->input->post("type_ids"));
					
					$i=1;
					foreach($type_ids as $type_id){
						$this->db->where("main_tabs_id",$this->input->post("main_tabs_id"));
						$this->db->where("product_id",$type_id)->update("main_tabs_to_product",array("sort_order"=>$i));
					
						$i++;
					}
				$this->quick_model->logs("tabs ".$this->input->post("main_tabs_id")." product sorted");
			exit;
			}
		
	}
	
	function remove_products(){
	
			if($this->input->is_ajax_request()){
			
				$this->db->where("main_tabs_id",$this->input->post("main_tabs_id"));
				$this->db->where("product_id",$this->input->post("product_id"))->delete("main_tabs_to_product");
				
				if($this->db->affected_rows()){
				$this->quick_model->logs("product ".$this->input->post("product_id")." removed from tab ".$this->input->post("main_tabs_id")."");
				}
			exit;
			}
	
	
	}
	
	function add_products(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("add");
		
			if($this->input->is_ajax_request()){
				
				$response["error"]=0;
				$response["message"]="";
				
				$insert_data=array(
					"product_id"=>$this->input->post("product_id"),
					"main_tabs_id"=>$this->input->post("main_tabs_id"),
				);
				
				$check =$this->db->where($insert_data)->get("main_tabs_to_product");
				
				if($check->num_rows>0){
				
				$response["error"]=1;
				$response["message"]=$this->language_model->language_c_key("alreadyassigned");//"The product is already assigned ?";
				echo json_encode($response);
				exit;
				}
				
				$this->db->insert("main_tabs_to_product",$insert_data);
				
				if($this->db->affected_rows()){
					$this->quick_model->logs("product ".$this->input->post("product_id")." added to tab ".$this->input->post("main_tabs_id")."");
					$response["product"]=($this->product_model->product_img($this->input->post("product_id"))->row_array());
				}
	
				echo json_encode($response);
				exit;
			
			}
	
	
	}
	
}