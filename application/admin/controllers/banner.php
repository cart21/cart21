<?php
class banner extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
     
        $this->load->model('product_model');
        $this->load->model('plugin_model');
        $this->load->library('page_position_lib');
        
        $this->load->library('image');
        
        $this->data["plugin"]=$this->plugin=$this->plugin_model->plugin_by_key("default_banner")->row();
        
    }
 
    function get_shape(){
   
    return array(
    			
    			"banner_id" => $this->data["L"]["id"],
    			"image" => $this->data["L"]["image"],
    			"description" => $this->data["L"]["description"],
    			"bl_id" => $this->data["L"]["id"],
				"language_id" => $this->data["L"]["language"]
			);
    
    }
    
    function where_work(){
    
    	if(isset($this->form_post_banner["language_id"])){
    		$this->db->where("language_id",$this->form_post_banner["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_banner)){
		
			$this->form_post_banner_where=array_diff_key($this->form_post_banner,$pattern=array("link"=>"link","class_routes"=>""));
			$this->db->where($this->form_post_banner_where);
			
			
			if(isset($this->form_post_banner["link"])){
				$this->db->like("link",$this->form_post_banner["link"]);
			}
			
		}else{
		$this->form_post_banner=array();
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
		
			$this->form_post_banner=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_banner',$this->form_post_banner);
		}else{
		
			$this->form_post_banner=$this->sessiondd->userdata('form_post_banner') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_banner;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('banner')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/banner/index';
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
		
		$banner_session=$this->sessiondd->userdata('banner');
		
		if (	isset($banner_session["orderby"])		){
		
		$this->db->order_by($banner_session["orderby"], $banner_session["orderby_order"]);
		}else{
		$this->db->order_by("banner_id", "desc");
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
 		
 		
 		$banners=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("banner");   ///segment 4 page
	
		$this->data["banners"]=$this->modules->banner_list($banners);
		
		if($action=="list"){
		echo $this->data["banners"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('banner/banner',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('description',  $this->data["L"]["description"], 'trim|required|xss_clean');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data_banner=array_diff_key($this->data["POST"],$pattern=array("plugin_to_page"=>" "));
	 		
	 		$data_banner["language_id"]=language_id();
	 	
	 		$this->db->insert("banner",$data_banner);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." idli banner added !");
	 		
	 		$this->db->where("banner_id",$ids)->update("banner",array("bl_id"=>$ids));
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>$ids,"plugin_id"=>$this->plugin->plugin_id));
	 		///plugin dynamic to page ///
	 		
	 		$this->upload($ids);
	 		
	 	redirect("admin/banner");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->quick->Header("");
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>0,"plugin_id"=>$this->plugin->plugin_id));
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('banner/banner_form',$this->data);
	
    }
    
    function edit($ids,$bl_id){
    	$this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->quick->Header("");
	    
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		
 
		$this->data["POST"]=$this->input->post();
		
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('description',"description", 'trim|required|xss_clean');
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data_banner=array_diff_key($this->data["POST"],$pattern=array("plugin_to_page"=>" "));
	 		$this->db->where("banner_id",$ids)->update("banner",$data_banner);
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>$bl_id,"plugin_id"=>$this->plugin->plugin_id));
	 		///plugin dynamic to page ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		$this->upload($ids);
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		
		$this->data["POST"]=$this->db->where("banner_id",$ids)->get("banner")->row_array();
		$this->category_language_create();
		
		$this->data["cl_group"]=$this->product_model->banner_group($this->data["POST"]["bl_id"])->result_array();
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>$bl_id,"plugin_id"=>$this->plugin->plugin_id));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('banner/banner_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$banners=$this->db->where_in("bl_id",$this->input->post("banner_id"))->get("banner");
			
			foreach($banners->result_array() as $bnr){
				
				$this->image->delete_img_thumb("/uploads/banner/".$bnr["image"]);
				$this->db->where("banner_id",$bnr["banner_id"])->delete("banner");
			
					
			}
		
			$this->quick_model->logs(implode(',',$this->input->post("banner_id"))." id banner deleted ");
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
		
			if($key=="banner_group"){
				$group=$this->db->where_in("banner_group_id",$value)->get("banner_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_banner',"");
	redirect('admin/banner');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$banner_session=$this->sessiondd->userdata('banner') ;
			
				$banner_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('banner',$banner_session);
			
				
				if(	isset($banner_session["orderby_order"]) ){
				
					if($banner_session["orderby_order"]=="asc" ){
					
						$banner_session["orderby_order"]="desc";
					}else{
					
						$banner_session["orderby_order"]="asc";
					}
					
				}else{
				
					$banner_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('banner',$banner_session);
			}
	
	redirect('admin/banner');
	}
	
	function upload($banner_id){
		
		if($_FILES["ImageFile"]["error"][0]!=4){
		
        $date_folder=date("Y/m/d",mktime());
        $dirpath="uploads/banner/".$date_folder;
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
			$banner=$this->db->where("bl_id",$banner_id)->get("banner");
		
			if($banner->num_rows){
				if($banner->row()->image){
				$this->image->delete_img_thumb("/uploads/banner/".$banner->row()->image);
				}
			
			}
			/// delete existing thumb ///
			
			$this->db->where("banner_id",$banner_id)->update("banner" ,array("image"=>$date_folder.'/'.$image["file_name"]));
			$this->image->create_thumb("/uploads/banner/".$date_folder.'/'.$image["file_name"]);
				
		}
		
		}
    }
 
    function delete_image(){
   
    $image_url=$this->db->where(array("banner_image_id"=>$this->input->post("file_id")) )->get("banner_image");
    $image_url=$image_url->row()->image_loc;
    
    $this->db->delete("banner_image",array("banner_image_id"=>$this->input->post("file_id")));
    
    unlink($_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
    exit;
    }
	
	function category_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join banner as pc
			on pc.language_id=l.language_id and pc.bl_id=".$this->data["POST"]["bl_id"]."
	
			where l.status=1 and ( pc.banner_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"bl_id"=>1,
					"description"=>1,
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("banner",$new_data);
			}
	
		}
	}
	
	
	
		
}