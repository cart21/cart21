<?php
class product_feature extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('product_model');
        
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_feature_id" =>$this->data["L"]["id"],
    			"feature_title" => $this->data["L"]["title"],
    			"feature_type" => $this->data["L"]["productfeaturetype"],
				"status"=>$this->data["L"]["status"],
				"f_id" => $this->data["L"]["id"],
				"language_id" => $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_product_feature["language_id"])){
    		$this->db->where("language_id",$this->form_post_product_feature["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",language_id());
    	}
    	

    	if($this->input->get("feature_type")){
    		$this->form_post_product_feature["feature_type"]=array($this->input->get("feature_type"));
    	}
		
		if(is_array($this->form_post_product_feature)){
		
			$this->form_post_product_feature_where=array_diff_key($this->form_post_product_feature,$pattern=array("feature_title"=>"feature_title","feature_type"=>"1"));
			$this->db->where($this->form_post_product_feature_where);
			
			
			if(isset($this->form_post_product_feature["feature_title"])){
			
				$this->db->or_like("lower(feature_title)",strtolower($this->form_post_product_feature["feature_title"]));
				
			}
			if(isset($this->form_post_product_feature["feature_type"])){
					
				$this->db->where_in("feature_type",($this->form_post_product_feature["feature_type"]));
			
			}
			
		}else{
		$this->form_post_product_feature=array();
		}	
		/// post1 *///	
    
		$this->sessiondd->set_userdata('form_post_product_feature',$this->form_post_product_feature);
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
		
			$this->form_post_product_feature=array_filter($this->input->post());
			
			$this->sessiondd->set_userdata('form_post_product_feature',$this->form_post_product_feature);
		}else{
		
			$this->form_post_product_feature=$this->sessiondd->userdata('form_post_product_feature') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_feature;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_feature')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=50;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_feature/index';
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
		
		$product_feature_session=$this->sessiondd->userdata('product_feature');
		
		if (	isset($product_feature_session["orderby"])		){
		
		$this->db->order_by($product_feature_session["orderby"], $product_feature_session["orderby_order"]);
		}else{
		$this->db->order_by("product_feature_id", "desc");
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
 		
 		$product_features=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_feature");   ///segment 4 page
	
		$this->data["product_features"]=$this->modules->product_feature_list($product_features);
		
		if($action=="list"){
		echo $this->data["product_features"];
		exit;
		}
		
		$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product_feature',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('feature_title', 'title', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('feature_type', 'Feature type', 'trim|required|xss_clean');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=$this->input->post();
	 		$this->data["POST"]["language_id"]=$this->data["settings"]["front_language"];
	 		
	 		$this->db->insert("product_feature",$this->data["POST"]);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		$this->db->where("product_feature_id",$ids)->update("product_feature",array("f_id"=>$ids));
	 		
	 		$this->quick_model->logs($ids." idli product_feature added !");
	 		
	 		
	 	redirect("admin/product_feature/edit/{$ids}/{$ids}");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
		
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_feature_form',$this->data);
	
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
    	 
    	if($this->input->post("product_feature_id")>0){
    
    		$this->db->where("product_feature_id",$this->input->post("product_feature_id"))->update("product_feature",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }
    
    function edit($ids,$f_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
	$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('feature_title', 'title', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('feature_type', 'Feature type', 'trim|xss_clean');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		
	 		$this->db->where("product_feature_id",$ids)->update("product_feature",$this->data["POST"]);
	 		
	 		$intersect_key=array(
	 				"feature_type"=>1
	 		);
	 		
	 		$f_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 				
	 			$this->db->where(array("f_id"=>$f_id,"language_id"=>$l["language_id"]))->update("product_feature",$f_new_data);
	 		}
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		redirect("admin/product_feature");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->product_model->product_feature($ids)->row_array();
		$this->fype_language_create();
		$this->data["f_group"]=$this->product_model->f_group($this->data["POST"]["f_id"])->result_array();
		
		$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_feature_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			foreach ($this->input->post("product_feature_id") as $f_id){
				$this->product_model->delete_product_feature($f_id);
			}
			
			$this->quick_model->logs(implode(',',$this->input->post("product_feature_id"))." idli product_features deleted ");
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
		
			if($key=="feature_type"){
				$group=$this->db->where_in("product_feature_type_id",$value)->get("product_feature_type")->result_array();
				$message.= "<p>".implode(",",array_column($group,"feature_type_title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_feature',"");
	redirect('admin/product_feature');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_feature_session=$this->sessiondd->userdata('product_feature') ;
			
				$product_feature_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_feature',$product_feature_session);
			
				
				if(	isset($product_feature_session["orderby_order"]) ){
				
					if($product_feature_session["orderby_order"]=="asc" ){
					
						$product_feature_session["orderby_order"]="desc";
					}else{
					
						$product_feature_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_feature_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_feature',$product_feature_session);
			}
	
	redirect('admin/product_feature');
	}
	

	function fype_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join product_feature as f
			on f.language_id=l.language_id and f.f_id=".$this->data["POST"]["f_id"]."
	
			where l.status=1 and ( f.product_feature_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"f_id"=>1,
					"feature_title"=>1,
					"feature_type"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product_feature",$new_data);
			}
	
		}
	}
	
		
}