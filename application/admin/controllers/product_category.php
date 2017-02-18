<?php
class product_category extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('category_model');
        $this->load->model('product_model');

        $this->load->library('image');
        
        $this->meta_type=5;
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_category_id" => $this->data["L"]["id"],
    			"image" => $this->data["L"]["image"],
    			"title" => $this->data["L"]["title"],
    			"slug" => $this->data["L"]["link"],
				"main_category_id" => $this->data["L"]["category"],
				"description" => $this->data["L"]["description"],
				"keywords" => $this->data["L"]["keywords"],
				"status" => $this->data["L"]["status"],
				"cl_id" => $this->data["L"]["id"],
				"language_id" => $this->data["L"]["language"]
				
			);
    }
    
    function where_work(){
    
    	if(isset($this->form_post_product_category["language_id"])){
    		$this->db->where("language_id",$this->form_post_product_category["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_product_category)){
		
			$this->form_post_product_category_where=array_diff_key($this->form_post_product_category,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_product_category_where);
			
			
			if(isset($this->form_post_product_category["link"])){
				$this->db->like("link",$this->form_post_product_category["link"]);
			}
			
			if(isset($this->form_post_product_category["class_routes"])){
				$this->db->like("class_routes",$this->form_post_product_category["class_routes"]);
			}
			
		}else{
		$this->form_post_product_category=array();
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
		
			$this->form_post_product_category=array_filter($this->input->post());
			if($this->input->post("status")==(-1)){
				$this->form_post_product_category["status"]=0;
			}
			$this->sessiondd->set_userdata('form_post_product_category',$this->form_post_product_category);
		}else{
		
			$this->form_post_product_category=$this->sessiondd->userdata('form_post_product_category') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_category;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_category')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_category/index';
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
		
		$product_category_session=$this->sessiondd->userdata('product_category');
		
		if (	isset($product_category_session["orderby"])		){
		
		$this->db->order_by($product_category_session["orderby"], $product_category_session["orderby_order"]);
		}else{
		$this->db->order_by("product_category_id", "desc");
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
 		
 		
 		$product_categorys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_category");   ///segment 4 page
	
		$this->data["product_categorys"]=$this->modules->product_category_list($product_categorys);
		
		if($action=="list"){
		echo $this->data["product_categorys"];
		exit;
		}
		
		$this->data["categories"]=$this->product_model->product_categories();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/category',$this->data);
	 
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
	 		
	 	
	 		$data_product_category=$this->input->post();
	 		
	 		$data_product_category["language_id"]=language_id();
	 		
	 		$this->db->insert("product_category",$data_product_category);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." idli product_category added !");
	 		$this->upload($ids);
	 		
	 		///meta 
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		//$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		
	 		$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 		$meta_data["class_routes"]="/category/index/".$ids;
	 		$meta_data["title"]=$this->input->post("title");
	 		$meta_data["language_id"]=$data_product_category["language_id"];
	 		$meta_data["type_l_id"]=$ids;
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"_cat");
	 		$this->db->where("product_category_id",$ids)->update("product_category",array("slug"=>$meta_data["link"],"cl_id"=>$ids));
	 		
	 		/// meta
	 		
	 	redirect("admin/product_category");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	$this->data["categories"]=$this->product_model->product_categories();
	
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/category_form',$this->data);
	
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
    	 
    	if($this->input->post("product_category_id")>0){
    
    		$this->db->where("product_category_id",$this->input->post("product_category_id"))->update("product_category",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    
    
    function edit($ids,$cl_id){
    
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
	 		
	 		$data_product_category=$this->data["POST"];
	 		
	 		$this->db->where("product_category_id",$ids)->update("product_category",$data_product_category);
	 		
	 		$intersect_key=array(
					"main_category_id"=>1,
					"image"=>1
			);
	 		
	 		$cl_new_data=array_intersect_key($data_product_category,$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 				
	 			$this->db->where(array("cl_id"=>$cl_id,"language_id"=>$l["language_id"]))->update("product_category",$cl_new_data);
	 		}
	 		
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		$meta=$this->meta_model->meta($meta_data);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		$meta_data["class_routes"]="/category/index/".$cl_id;
	 		$meta_data["language_id"]=$this->input->post("language_id");
	 		$meta_data["type_l_id"]=$cl_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 		
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data);
	 		$this->db->where("product_category_id",$ids)->update("product_category",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		$this->upload($cl_id);
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		//$this->data["product_category_types"]=$this->category_model->product_category_types()->result_array();
		$this->data["POST"]=$this->category_model->product_category($ids)->row_array();
		$this->category_language_create();
		
		$this->data["categories"]=$this->product_model->product_categories($this->data["POST"]["language_id"]);
		
		$this->data["cl_group"]=$this->product_model->cl_group($this->data["POST"]["cl_id"])->result_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/category_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			
			/// delete image ///
			$categories=$this->db->where_in("cl_id",$this->input->post("product_category_id"))->get("product_category");
				
			foreach ($categories->result_array() as $c ){
				$image_url=$c["image"];
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/uploads/product_category/".$image_url)){
					$this->image->delete_img_thumb("/uploads/product_category/".$image_url);
				}
			}
			/// delete image ///
			
			/// delete meta ///
			$this->db->where("type",$this->meta_type);
			$this->db->where_in("type_l_id",$this->input->post("product_category_id"))->delete("meta");
			/// delete meta ///
			
			$this->db->where_in("cl_id",$this->input->post("product_category_id"))->delete("product_category");
			$this->db->where_in("category_id",$this->input->post("product_category_id"))->delete("product_to_category");
			
			$this->quick_model->logs(implode(',',$this->input->post("product_category_id"))." id product_category deleted !");
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
		
			if($key=="product_category_group"){
				$group=$this->db->where_in("product_category_group_id",$value)->get("product_category_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_category',"");
	redirect('admin/product_category');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_category_session=$this->sessiondd->userdata('product_category') ;
			
				$product_category_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_category',$product_category_session);
			
				
				if(	isset($product_category_session["orderby_order"]) ){
				
					if($product_category_session["orderby_order"]=="asc" ){
					
						$product_category_session["orderby_order"]="desc";
					}else{
					
						$product_category_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_category_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_category',$product_category_session);
			}
	
	redirect('admin/product_category');
	}
	
	function upload($product_category_id){
	
		
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
        $date_folder=date("Y/m/d",mktime());
        $dirpath="uploads/product_category/".$date_folder;
        create_dir($dirpath);
        
        $config['upload_path'] =$_SERVER["DOCUMENT_ROOT"]. "/".$dirpath;
       
		//$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		foreach($result["success"] as $image){
			
			//delete existing previous
			$product_category=$this->db->where("cl_id",$product_category_id)->get("product_category");
			
			$image_url=$product_category->row()->image;
	
			if(file_exists("/uploads/product_category/".$image_url)){
				$this->image->delete_img_thumb("/uploads/product_category/".$image_url);
			}
			/// delete existing thumb ///
			
			$this->db->where("cl_id",$product_category_id)->update("product_category" ,array("image"=>$date_folder.'/'.$image["file_name"]));
			$this->image->create_thumb("/uploads/product_category/".$date_folder.'/'.$image["file_name"]);
				
		}
		
		}
    }
 
    function delete_image(){
   
    $image_url=$this->db->where(array("product_category_image_id"=>$this->input->post("file_id")) )->get("product_category_image");
    $image_url=$image_url->row()->image_loc;
    
    $this->db->delete("product_category_image",array("product_category_image_id"=>$this->input->post("file_id")));
    
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
	
	function category_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join product_category as pc
			on pc.language_id=l.language_id and pc.cl_id=".$this->data["POST"]["cl_id"]."
	
			where l.status=1 and ( pc.product_category_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"cl_id"=>1,
					"main_category_id"=>1,
					"image"=>1,
					"content"=>1,
					"title"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product_category",$new_data);
			}
	
		}
	}
	
	
	
		
}