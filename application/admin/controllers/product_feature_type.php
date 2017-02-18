<?php
class product_feature_type extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('product_model');
        
        $this->data["product_type"]=array_column($this->product_model->product_types()->result_array(),"title","pl_id");
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_feature_type_id" => $this->data["L"]["id"],
    			"feature_type_title" => $this->data["L"]["title"],
    			"sort_order" => $this->data["L"]["sorting"],
				"status"=>$this->data["L"]["status"],
				"ft_id" => $this->data["L"]["id"],
				"product_type_id" => $this->data["L"]["product_type"],
				"language_id" => $this->data["L"]["language"]

			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_product_feature_type["language_id"])){
    		$this->db->where("language_id",$this->form_post_product_feature_type["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_product_feature_type)){
		
			$this->form_post_product_feature_type_where=array_diff_key($this->form_post_product_feature_type,$pattern=array("product_type_id"=>"1","feature_type_title"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_product_feature_type_where);
			
			if(isset($this->form_post_product_feature_type["product_type_id"])){
			
				$this->db->where_in("product_type_id",($this->form_post_product_feature_type["product_type_id"]));
				
			}
			
			if(isset($this->form_post_product_feature_type["feature_type_title"])){
					
				$this->db->or_like("lower(feature_type_title)",strtolower($this->form_post_product_feature_type["feature_type_title"]));
			
			}
				
		}else{
		$this->form_post_product_feature_type=array();
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
		
			$this->form_post_product_feature_type=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_product_feature_type',$this->form_post_product_feature_type);
		}else{
		
			$this->form_post_product_feature_type=$this->sessiondd->userdata('form_post_product_feature_type') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_feature_type;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_feature_type')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_feature_type/index';
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
		
		$product_feature_type_session=$this->sessiondd->userdata('product_feature_type');
		
		if (	isset($product_feature_type_session["orderby"])		){
		
		$this->db->order_by($product_feature_type_session["orderby"], $product_feature_type_session["orderby_order"]);
		}else{
		$this->db->order_by("product_feature_type_id", "desc");
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
 		
 		
 		$product_feature_types=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_feature_type");   ///segment 4 page
		
		$this->data["product_feature_types"]=$this->modules->product_feature_type_list($product_feature_types);
		
		if($action=="list"){
		echo $this->data["product_feature_types"];
		exit;
		}
		

		$this->data["product_types"]=$this->product_model->product_types();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product_feature_type',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	$this->quick->Header("");
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->data["POST"]=array_filter($this->input->post());
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	 
	    
		$this->form_validation->set_rules('feature_type_title', $this->data["L"]["title"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('product_type_id', $this->data["L"]["product_type"], 'trim|required|xss_clean');
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=$this->input->post();
	 		$this->data["POST"]["language_id"]=language_id();
	 		
	 		$this->db->insert("product_feature_type",$this->data["POST"]);
	 		
	 		$ids=$this->db->insert_id();
	 		
	 		$this->db->where("product_feature_type_id",$ids)->update("product_feature_type",array("ft_id"=>$ids));
	 		
	 		$this->quick_model->logs($ids." idli product_feature_type added !");
	 		
	 		
	 	redirect("admin/product_feature_type/edit/{$ids}/{$ids}");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["product_types"]=$this->product_model->product_types();
		$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_feature_type_form',$this->data);
	
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
    
    	if($this->input->post("product_feature_type_id")>0){
    
    		$this->db->where("product_feature_type_id",$this->input->post("product_feature_type_id"))->update("product_feature_type",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function edit($ids,$ft_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		$this->quick->Header("");
   	
	$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('feature_type_title', $this->data["L"]["title"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('product_type_id', $this->data["L"]["product_type"], 'trim|required|xss_clean');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$this->data["POST"]=$this->input->post();
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		$this->db->where("product_feature_type_id",$ids)->update("product_feature_type",$this->data["POST"]);
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		$intersect_key=array(
	 				"product_type_id"=>1,
	 				"sort_order"=>1,
					"sub_type_id"=>1
	 		);
	 		
	 		$l_new_data=array_intersect_key($this->data["POST"],$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 		
	 			$this->db->where(array("ft_id"=>$ft_id,"language_id"=>$l["language_id"]))->update("product_feature_type",$l_new_data);
	 		}
	 		
	 		redirect("/admin/product_feature_type");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->product_model->product_feature_type($ids)->row_array();
	
		$this->ftype_language_create();
		$this->data["ft_group"]=$this->product_model->ft_group($this->data["POST"]["ft_id"])->result_array();

		$this->data["product_types"]=$this->product_model->product_types();
		$this->data["product_feature_types"]=$this->product_model->product_feature_types()->result_array();
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_feature_type_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			$this->product_model->delete_product_feature_type($this->input->post("product_feature_type_id"));
			
			$this->quick_model->logs(implode(',',$this->input->post("product_feature_type_id"))." idli product_feature_typelar deleted ");
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
		
			if($key=="product_type_id"){
				$group=$this->db->where_in("product_type_id",$value)->get("product_type")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_feature_type',"");
	redirect('admin/product_feature_type');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_feature_type_session=$this->sessiondd->userdata('product_feature_type') ;
			
				$product_feature_type_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_feature_type',$product_feature_type_session);
			
				
				if(	isset($product_feature_type_session["orderby_order"]) ){
				
					if($product_feature_type_session["orderby_order"]=="asc" ){
					
						$product_feature_type_session["orderby_order"]="desc";
					}else{
					
						$product_feature_type_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_feature_type_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_feature_type',$product_feature_type_session);
			}
	
	redirect('admin/product_feature_type');
	}
	
	function upload($product_feature_type_id){
	
		
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
		$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/product_feature_type/".$product_feature_type_id;
		
        if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755);
        }
		
        $config['upload_path'] ='./uploads/product_feature_type/'.$product_feature_type_id.'/';
       
		//$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		foreach($result["success"] as $image){
			
			$image_data=array(
			"image_loc"=>'uploads/product_feature_type/'.$product_feature_type_id.'/'.$image["file_name"],
			"product_feature_type_id"=>$product_feature_type_id,
			"title"=>str_replace($image["file_ext"],"",$image["client_name"]),
			"file_ext"=>substr($image["file_ext"],1),
			"date_added"=>mktime()
			);
			$this->db->insert("product_feature_type_image",	$image_data	);
			
		}
		
		}
    }
 
    function delete_image(){
   
    $image_url=$this->db->where(array("product_feature_type_image_id"=>$this->input->post("file_id")) )->get("product_feature_type_image");
    $image_url=$image_url->row()->image_loc;
    
    $this->db->delete("product_feature_type_image",array("product_feature_type_image_id"=>$this->input->post("file_id")));
    
    unlink($_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
    exit;
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
	
	function ftype_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join product_feature_type as ft
			on ft.language_id=l.language_id and ft.ft_id=".$this->data["POST"]["ft_id"]."
	
			where l.status=1 and ( ft.product_feature_type_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"ft_id"=>1,
					"feature_type_title"=>1,
					"product_type_id"=>1,
					"sort_order"=>1,
					"sub_type_id"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product_feature_type",$new_data);
			}
	
		}
	}
	
		
}