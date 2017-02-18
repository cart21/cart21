<?php
class product_property extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('product_property_model');
        $this->load->model('product_model');
        
        $this->meta_type=10;
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_property_id" => $this->data["L"]["id"],
				"title" => $this->data["L"]["title"],
    			"image" => $this->data["L"]["image"],
    			"slug" => $this->data["L"]["link"],
				"description" => $this->data["L"]["description"],
				"keywords" => $this->data["L"]["keywords"],
				"status" => $this->data["L"]["status"],
				"pl_id" => $this->data["L"]["id"],
				"language_id" => $this->data["L"]["language"]
				
			);
    
    }
    
    function where_work(){
    	
    	if(isset($this->form_post_product_property["language_id"])){
    		$this->db->where("language_id",$this->form_post_product_property["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id", language_id());
    	}
    
		if(is_array($this->form_post_product_property)){
		
			$this->form_post_product_property_where=array_diff_key($this->form_post_product_property,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_product_property_where);
			
			
			if(isset($this->form_post_product_property["link"])){
				$this->db->like("link",$this->form_post_product_property["link"]);
			}
			
			if(isset($this->form_post_product_property["class_routes"])){
				$this->db->like("class_routes",$this->form_post_product_property["class_routes"]);
			}
			
		}else{
		$this->form_post_product_property=array();
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
		
			$this->form_post_product_property=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_product_property',$this->form_post_product_property);
		}else{
		
			$this->form_post_product_property=$this->sessiondd->userdata('form_post_product_property') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_property;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_property')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_property/index';
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
		
		$product_property_session=$this->sessiondd->userdata('product_property');
		
		if (	isset($product_property_session["orderby"])		){
		
		$this->db->order_by($product_property_session["orderby"], $product_property_session["orderby_order"]);
		}else{
		$this->db->order_by("product_property_id", "desc");
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
 		
 		$product_propertys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_property");   ///segment 4 page
	
		$this->data["product_propertys"]=$this->modules->product_property_list($product_propertys);
		
		if($action=="list"){
		echo $this->data["product_propertys"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product_property',$this->data);
	 
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
	 		
	 		$data_product_property=$this->input->post();
			$data_product_property["language_id"]=language_id();
	 		
	 		$this->db->insert("product_property",$data_product_property);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		///meta
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		
	 		$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 		$meta_data["class_routes"]="/product_property/index/".$ids;
	 		$meta_data["title"]=$this->input->post("title");
	 		$meta_data["language_id"]=$data_product_property["language_id"];
	 		$meta_data["type_l_id"]=$ids;
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data,"_prop");
	 		$this->db->where("product_property_id",$ids)->update("product_property",array("slug"=>$meta_data["link"],"pl_id"=>$ids));
	 		
	 		/// meta
	 		
	 		$this->quick_model->logs($ids." idli product_property added !");
	 		$this->upload($ids);
	 		
	 	redirect("admin/product_property/edit/".$ids."/".$ids);
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
	$this->smarty->view('product/product_property_form',$this->data);
	
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
    	 
    	if($this->input->post("product_property_id")>0){
    
    		$this->db->where("product_property_id",$this->input->post("product_property_id"))->update("product_property",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    
    
    function edit($ids,$pl_id){
    
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
	 		
	 		$data_product_property=$this->data["POST"];
	 		
	 		$this->db->where("product_property_id",$ids)->update("product_property",$data_product_property);
	 		
	 		$meta_data=array("type"=>$this->meta_type,"type_id"=>$ids);
	 		
	 		$meta=$this->meta_model->meta($meta_data);
	 		
	 		$meta_data["description"]=strip_tags($this->input->post("description"));
	 		$meta_data["keywords"]=strip_tags($this->input->post("keywords"));
	 		$meta_data["link"]=strip_tags($this->input->post("slug"));
	 		$meta_data["language_id"]=$this->input->post("language_id");
	 		$meta_data["type_l_id"]=$pl_id;
	 		
	 		if($meta->num_rows){
	 		
	 			$this->meta_model->update_meta($meta_data);
	 		}else{
	 		
	 			$meta_data["link"]=$this->quick->toAscii($this->input->post("title")).".html";
	 			
	 			$meta_data["class_routes"]="/product_property/index/".$ids;
	 			$meta_data["title"]=$this->input->post("title");
	 		
	 		$meta_data=$this->meta_model->insert_meta($meta_data);
	 		$this->db->where("product_property_id",$ids)->update("product_property",array("slug"=>$meta_data["link"]));
	 		}
	 		
	 		$this->upload($pl_id);
	 		
	 		$this->quick->success[]="Successfull";
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->product_property_model->product_property($ids)->row_array();
		$this->property_language_create();
		$this->data["pl_group"]=$this->product_model->propertyl_group($this->data["POST"]["pl_id"])->result_array();
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_property_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			$this->db->where_in("product_property_id",$this->input->post("product_property_id"))->delete("product_property");
			/// delete meta ///
			$this->db->where("type", $this->meta_type);
			$this->db->where_in("type_id",$this->input->post("product_property_id"))->delete("meta");
			/// delete meta ///
			
			$this->quick_model->logs(implode(',',$this->input->post("product_property_id"))." idli product_propertylar silindi ");
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
		
			if($key=="product_property_group"){
				$group=$this->db->where_in("product_property_group_id",$value)->get("product_property_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_property',"");
	redirect('admin/product_property');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_property_session=$this->sessiondd->userdata('product_property') ;
			
				$product_property_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_property',$product_property_session);
			
				
				if(	isset($product_property_session["orderby_order"]) ){
				
					if($product_property_session["orderby_order"]=="asc" ){
					
						$product_property_session["orderby_order"]="desc";
					}else{
					
						$product_property_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_property_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_property',$product_property_session);
			}
	
	redirect('admin/product_property');
	}
	
	function upload($product_property_id){
	
		
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
		$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/product_property/".$product_property_id;
		
        if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755);
        }
		
        $config['upload_path'] ='./uploads/product_property/'.$product_property_id.'/';
       
		//$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		foreach($result["success"] as $image){
			
			$this->db->where("product_property_id",$product_property_id)->update("product_property" ,array("image"=>'uploads/product_property/'.$product_property_id.'/'.$image["file_name"]));	
			
		}
		
		}
    }
 
    function delete_image(){
   
    $image_url=$this->db->where(array("product_property_image_id"=>$this->input->post("file_id")) )->get("product_property_image");
    $image_url=$image_url->row()->image_loc;
    
    $this->db->delete("product_property_image",array("product_property_image_id"=>$this->input->post("file_id")));
    
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
	
	function property_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join product_property as pp
			on pp.language_id=l.language_id and pp.pl_id=".$this->data["POST"]["pl_id"]."
	
		where l.status=1 and ( pp.product_property_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"pl_id"=>1,
					"title"=>1,
					"image"=>1,
					"content"=>1,
					"keywords"=>1,
					"description"=>1,
					"con_title"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product_property",$new_data);
			}
	
		}
	}
	
		
}