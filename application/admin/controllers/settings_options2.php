<?php
class settings_options2 extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();

        $this->load->model('quick_model');
        $this->load->model('content_model');
        $this->load->model('category_model');
        $this->load->model('language_model');
        
        $this->load->helper("directory");
       
    }
  
 	function get_shape(){
   
    return array(
    			
    			"settings_options_id" => $this->data["L"]["id"],
    			"site_title" => $this->data["L"]["title"],
    			"offline" => $this->data["L"]["status"],
    			"logo" => $this->data["L"]["logo"],
    			"site_url" => $this->data["L"]["url"],
    			"email" => $this->data["L"]["email"],
    			"smtp_host" => "smtp_host",
    			"smtp_port" => "smtp_port",
    			"smtp_user" => "smtp_user",
    			"smtp_pass" => "smtp_pass",
    			"smtp_sendername" => "smtp_sendername",
    			
			);
    
    }
    
    function where_work(){
    
    ///post1 ///

		if( $this->input->post()){
		
			$this->form_post_settings_options=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_settings_options',$this->form_post_settings_options);
		}else{
		
			$this->form_post_settings_options=$this->sessiondd->userdata('form_post_settings_options') ;
		}
			
			
		if(is_array($this->form_post_settings_options)){
		
			$this->form_post_settings_options_where=array_diff_key($this->form_post_settings_options,$pattern=array(
					"your_capacity"=>"your_capacity",
					"site_title"=>"site_title",
					"site_url"=>"site_url"));
			
			if(isset($this->form_post_settings_options["site_url"])){
				
				$this->db->like("site_url",$this->form_post_settings_options["site_url"]);
			}
			if(isset($this->form_post_settings_options["site_title"])){
			
				$this->db->like("site_title",$this->form_post_settings_options["site_title"]);
			}
			
			$this->db->where($this->form_post_settings_options_where);
			
			
			
		}else{
		$this->form_post_settings_options=array();
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
		

		$this->where_work();

		$this->data["POST"]= $this->form_post_settings_options;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('settings_options')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=15;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/settings_options/index';
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
		
		$settings_options_session=$this->sessiondd->userdata('settings_options');
		
		if (	isset($settings_options_session["orderby"])		){
		
		$this->db->order_by($settings_options_session["orderby"], $settings_options_session["orderby_order"]);
		}else{
		$this->db->order_by("settings_options_id", "desc");
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
 		
 		$settings_optionss=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("settings_options");   ///segment 4 page
		$this->data["site_settingss"]=$this->modules->settings_options_list($this->smarty,$settings_optionss);
		
		if($action=="list"){
		echo $this->data["settings_optionsS"];
		exit;
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('site_settings/site_settings',$this->data);
	 
	}
	
	function add(){
	}
    
    function delete(){
    
    	if($this->permission->check_permission("delete") ){
    		$this->db->where_in("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("status"=>"0"));
    		$this->quick_model->logs(implode(',',$this->input->post("settings_options_id"))." idli Oda silindi ");
    	$result="1";
		}else{
			$result="0";
		}
	   	echo $result;
    	exit;	
    }
    
    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 

    	 
    	if($this->input->post("status")=="true"){
    
    		$status=0;
    	}else {
    
    		$status=1;
    	}
    	 
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("offline"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    

     function change_use_smtp(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	if($this->input->post("use_smtp")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("use_smtp"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    } 
    
    function change_social_login(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("social_login")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("social_login"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }

    function change_search_engine(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("search_engine")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("search_engine"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }

    function main_menu(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("main_menu")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("main_menu"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }    

    function menu_product_category(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("menu_product_category")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("settings_options_id")>0){
    
    		$this->db->where("settings_options_id",$this->input->post("settings_options_id"))->update("settings_options",array("menu_product_category"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function social_link_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("social_link_id")>0){
    
    		$this->db->where("social_link_id",$this->input->post("social_link_id"))->update("social_link",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    
    function edit_social_link($ids){
    	 
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->ids=$ids;
    	$this->data["page"]="edit";
    	 
    	$this->load->helper(array('form', 'url'));
    	$this->load->library('form_validation');
    	 
    	$this->data["content_category"]=$this->content_model->content_categories();
    	 
    	$this->form_validation->set_rules('title', $this->language_model->language_c_key("title"), 'trim|required|xss_clean');
    	 
    	///post
    	if( $this->input->post() and $this->permission->check_permission("edit") ){
    		 
    		if ($this->form_validation->run()) {
    
    			$data =$this->input->post();
    			 
    			if($this->input->post("status")=="on"){
    					
    				$data["status"]=1;
    			}else {
    					
    				$data["status"]=0;
    			}
    		
    			 
    			$this->db->where("social_link_id",$ids)->update('social_link', $data);
    			 
    			 
    			 
    		}else{
    			$this->quick->errors[] = validation_errors();
    		}
    
    	}
    	 
    	 
    	$this->data["POST"]=$this->db->where("social_link_id",$ids)->get("social_link")->row_array();
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    
    	$this->smarty->view('site_settings/social_link_form',$this->data);
    
    }
    
    
    
    function edit($ids){
   
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
		$this->ids=$ids;
		$this->data["page"]="edit";
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		$this->data["content_category"]=$this->content_model->content_categories();
		
		$this->form_validation->set_rules('site_title', 'site title ', 'trim|required|xss_clean');
		
		///post
		if( $this->input->post() and $this->permission->check_permission("edit") ){
		
			if ($this->form_validation->run()) {
			
				$data =$this->input->post();
				
				///
		 		if($this->input->post("offline")=="on"){
		 		
		 			$data["offline"]=0;
		 		}else {
		 		
		 			$data["offline"]=1;
		 		}
		 		
		 		if($this->input->post("search_engine")=="on"){
		 			 
		 			$data["search_engine"]=1;
		 		}else {
		 			 
		 			$data["search_engine"]=0;
		 		}

		 		if($this->input->post("social_login")=="on"){
		 				
		 			$data["social_login"]=1;
		 		}else {
		 				
		 			$data["social_login"]=0;
		 		}
		 		
		 		if($this->input->post("use_smtp")=="on"){
		 			 
		 			$data["use_smtp"]=1;
		 		}else {
		 			 
		 			$data["use_smtp"]=0;
		 		}
		 		
		 		///
				
				$this->db->where("settings_options_id",$ids)->update('settings_options', $data);
				
				$this->upload($ids);
				
				$this->quick->success[]=$data["site_title"]." ".$this->language_model->language_c_key("successfuledit");
				$this->quick_model->logs($data["site_title"]." ".$this->language_model->language_c_key("successfuledit"));
				
			//redirect("admin/settings_options2");
			}else{
				$this->quick->errors[] = validation_errors();
			}
		}
		
		///post
		
			
			$this->data["social_links"]=$this->quick_model->social_links();
			$this->data["currencies"]=$this->quick_model->currencies()->result_array();
		
			$this->data["POST"]=$this->db->where("settings_options_id",$ids)->get("settings_options")->row_array();
			
			$this->data["front_themes"]=directory_map($_SERVER['DOCUMENT_ROOT']."/application/front/views/templates/",1);
			
			$this->quick->Header("");
			$this->quick->Top_menu("");
			$this->quick->Footer("");
		$this->smarty->view('site_settings/site_settings_form',$this->data);
    
    }
    
    function upload($settings_options_id){
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
      
        $dirpath="uploads/";
        
        $config['upload_path'] =$_SERVER["DOCUMENT_ROOT"]. "/".$dirpath;
       
		$config['file_name'] ="logo";
		$config['overwrite'] = true;
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		
		$this->load->library('upload', $config);
		$result=$this->quick->upload("ImageFile");
		
		foreach($result["success"] as $image){
			
			$this->db->where("settings_options_id",$settings_options_id)->update("settings_options" ,array("logo"=>"/".$dirpath.$image["file_name"]));
				
		}
		
		}
    }
    
    function delete_image(){
    
    //echo $this->input->post("image_id");
    
    $image_url=$this->db->where(	array("image_id"=>$this->input->post("image_id")) )->get("images");
    
	//$this->dbg2($image_url->row_array()); exit;
    $image_url=$image_url->row()->image_url;
    
    
    
    $this->db->delete("images",array("image_id"=>$this->input->post("image_id")));
    $this->db->delete("settings_options_to_images",array("image_id"=>$this->input->post("image_id")));
    
    unlink( $_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
    exit;
    }
    
	function get_title($row_array){
 
	 
	
	/*		
	 $this->shape=array(
				"rezervation_id" => "ID",
				"firstname" =>"firstname",
				"lastname" => "lastname"
				
			);
		*/			
	//$this->dbg2( $this->shape);
	
	$keys=array_keys(array_intersect_key($row_array,$this->shape));
	//$this->dbg2($keys);
	
	$translated=array_map(array($this,"translate_key"),$keys);
	
	//$this->dbg2($translated);  
    
 return $translated;
 }

	function translate_key ($value){

 return $this->shape[$value];
 }
 
 	function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
		
		if(in_array($key,array("capacity","begin_date","expire_date"))){
		
		$message.= "<p>".$key.":".$value."</p>";
		continue;
		}
		$message.= "<p>".$this->shape[$key].":".$value."</p>";
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_settings_options',"");
	redirect('admin/settings_options2');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$settings_options_session=$this->sessiondd->userdata('settings_options') ;
			
				$settings_options_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('settings_options',$settings_options_session);
			
				
				if(	isset($settings_options_session["orderby_order"]) ){
				
					if($settings_options_session["orderby_order"]=="asc" ){
					
						$settings_options_session["orderby_order"]="desc";
					}else{
					
						$settings_options_session["orderby_order"]="asc";
					}
					
				}else{
				
					$settings_options_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('settings_options',$settings_options_session);
			}
	
	redirect('admin/settings_options');
	}
	
	
	function check_settings_options_code($code){

 		$settings_options=$this->db->where(array("code"=>$code,"status"=>0))->get("settings_options");
    	
    	if($settings_options->num_rows==0){
    	return true;
    	}else{
    	 $this->quick->errors[]=$code." Bu kodile daha önce kayıt var ";
    	return false;
    	}
 
 }
 
	function check_settings_options_code_edit($code){

		$this->db->where("settings_options_id <>",$this->ids);
 		$settings_options=$this->db->where(array("code"=>$code,"status"=>0))->get("settings_options");
    	
    	if($settings_options->num_rows==0){
    	return true;
    	}else{
    	// $this->quick->errors[]=$code." Bu kod ile daha önce kayıt var ";
    	$this->form_validation->set_message('check_settings_options_code_edit', ' Bu kod ile daha önce kayıt var');
    	return false;
    	}
    	
 }


	
		
}