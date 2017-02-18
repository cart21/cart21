<?php
class meta extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        $this->load->model('meta_model');
        
        $this->meta_types=array_filter(array_column($this->meta_model->meta_types()->result_array(),"table_name","meta_type_id"));
        
        
    }
  
    function get_shape(){
   
    return array(
    			
    			"meta_id" => $this->data['L']["id"],
    			"m_id" => $this->data['L']["id"],
    			"language_id" => $this->data['L']["language"],
    			"link" => $this->data['L']["link"],
    			"class_routes" =>$this->data['L']["route"],
				"title" => $this->data['L']["title"],
				"description" =>$this->data['L']["description"] ,
				"keywords" => $this->data['L']["keywords"],
				"type" => $this->data['L']["metatype"],
				"type_id" => $this->data['L']["id"],
				"footer" => $this->data['L']["footer"],
				"top_menu" => $this->data['L']["topmenu"],
				"main_menu" => $this->data['L']["main_menu"],
				"no_delete" => "No Delete"

			);
    
    }
    
    function where_work(){
    
    	
    	if(isset($this->form_post_meta["language_id"])){
    		$this->db->where("language_id",$this->form_post_meta["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id", language_id());
    	}
		if(is_array($this->form_post_meta)){
		
			$this->form_post_meta_where=array_diff_key($this->form_post_meta,$pattern=array("link"=>"link","class_routes"=>"class_routes","type"=>"type","title"=>"title"));
			$this->db->where($this->form_post_meta_where);
			

			if(isset($this->form_post_meta["type"])){
				$this->db->where_in("type",$this->form_post_meta["type"]);
			}
			if(isset($this->form_post_meta["link"])){
				$this->db->like("link",$this->form_post_meta["link"]);
			}
			
			if(isset($this->form_post_meta["class_routes"])){
				$this->db->like("class_routes",$this->form_post_meta["class_routes"]);
			}

			if(isset($this->form_post_meta["title"])){
				$this->db->like("lower(title)",strtolower($this->form_post_meta["title"]));
			}
			
		}else{
		$this->form_post_meta=array();
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
		
			$this->form_post_meta=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_meta',$this->form_post_meta);
		}else{
		
			$this->form_post_meta=$this->sessiondd->userdata('form_post_meta') ;
		}
		
		if( $this->input->get("meta_type_id")){
		
			$this->form_post_meta["type"]=array($this->input->get("meta_type_id"));
			$this->sessiondd->set_userdata('form_post_meta',$this->form_post_meta);
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_meta;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('meta')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/meta/index';
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
		
		$meta_session=$this->sessiondd->userdata('meta');
		
		//$this->dbg2($meta_session);
		if (	isset($meta_session["orderby"])		){
		
		$this->db->order_by($meta_session["orderby"], $meta_session["orderby_order"]);
		}else{
		$this->db->order_by("meta_id", "desc");
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
 		
 		$metas=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("meta");   ///segment 4 page
		
		$this->data["metas"]=$this->modules->meta_list($this->smarty,$metas);
		
		
		if($action=="list"){
		echo $this->data["metas"];
		exit;
		}
		
		$this->data["meta_types"]=$this->meta_model->meta_types()->result_array();
			
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('meta/meta',$this->data);
	 
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
		$this->form_validation->set_rules('link', 'link', 'trim|required|xss_clean|callback_check_slug');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data["title"]=$this->input->post("title");
	 		$data["link"]=$this->input->post("link");
	 		$data["class_routes"]=$this->input->post("class_routes");
	 		$data["redirect"]=$this->input->post("redirect");
	 		$data["description"]=$this->input->post("description");
	 		$data["keywords"]=$this->input->post("keywords");
	 		
	 		$data["type"]=$this->input->post("type");
	 		$data["type_id"]=$this->input->post("type_id");
	 		$data["language_id"]=language_id();
	 		
	 		$this->db->insert("meta",$data);
	 		
	 		$ids=$this->db->insert_id();
	 		$this->db->where("meta_id",$ids)->update("meta",array("m_id"=>$ids));
	 		
	 		/// assign to menu ///
	 		$m_id=$ids;
	 		if($this->input->post("main_menu_id")){
	 			foreach($this->input->post("main_menu_id") as $top_m_id){
	 				if($top_m_id!=$m_id){
	 					$this->db->insert("link_to_menu",array("top_m_id"=>$top_m_id,"sub_m_id"=>$m_id));
	 				}
	 			}
	 		}
	 		/// assign to menu ///
	 		
	 		
	 		$this->upload($ids);
	 		
	 		$this->quick_model->logs($ids." id meta added  ");
	 		
	 	redirect("admin/meta");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["meta_types"]=$this->meta_model->meta_types()->result_array();
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		/// assign to menu ///
		$this->data["main_menu_category"]=$this->meta_model->main_menu_tree();
		$this->data["lucky_main_menu"]=array();
		/// assign to menu ///
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('meta/meta_form',$this->data);
	
    }
    
    function edit($ids,$m_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
	$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('link', 'link', 'trim|required|xss_clean|callback_check_slug_edit');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data["title"]=$this->input->post("title");
	 		$data["link"]=$this->input->post("link");
	 		$data["class_routes"]=$this->input->post("class_routes");
	 		$data["redirect"]=$this->input->post("redirect");
	 		$data["description"]=$this->input->post("description");
	 		$data["keywords"]=$this->input->post("keywords");
	 		$data["main_menu_sort"]=$this->input->post("main_menu_sort");
	 		$data["type"]=$this->input->post("type");
	 		$data["type_id"]=$this->input->post("type_id");
	 		
	 		$this->db->where("meta_id",$ids)->update("meta",$data);
	 		
	 		if(! in_array($data["type"],array(1,9))){
	 		$this->sync_data($data["type"],$data["type_id"],$data["link"]);
	 		}
	 		
	 		/// assign to menu ///
	 		$this->db->query("DELETE FROM `link_to_menu` WHERE sub_m_id={$m_id} and top_m_id<>0");
	 		if($this->input->post("main_menu_id")){
	 			foreach($this->input->post("main_menu_id") as $top_m_id){
	 				if($top_m_id!=$m_id){
	 				$this->db->insert("link_to_menu",array("top_m_id"=>$top_m_id,"sub_m_id"=>$m_id));
	 				}
	 			}
	 		}
	 		/// assign to menu ///
	 		
	 		$this->upload($m_id);
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		

	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["meta_types"]=$this->meta_model->meta_types()->result_array();
		$this->data["POST"]=$this->db->where("meta_id",$ids)->get("meta")->row_array();
		
		
			
			$this->meta_model->meta_language_create($this->data["POST"]);
			
			/// plugin ///
			$plugin=$this->plugin_model->page_plugin($this->data["POST"]["m_id"]);
			foreach ($plugin->result_array() as $p){
					
				$this->data["positions"][$p["position"]][]=$p;
			}
			//dbg($this->data["positions"]);exit;
			/// plugin ///
		
		$this->data["m_group"]=$this->meta_model->m_group($this->data["POST"]["m_id"])->result_array();
		
		/// assign to menu ///
		$this->data["main_menu_category"]=$this->meta_model->main_menu_tree();
		
		$lucky_main_menu=$this->meta_model->lucky_main_menu($this->data["POST"]["m_id"]);
		if($lucky_main_menu->num_rows){
			$this->data["lucky_main_menu"]=array_column($lucky_main_menu->result_array(),"top_m_id");
		}else{
			$this->data["lucky_main_menu"]=array();
		}
		/// assign to menu ///
		
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('meta/meta_form',$this->data);
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		//	$this->db->where("no_delete",0);
			$this->db->where_in("meta_id",$this->input->post("meta_id"))->delete("meta");
			$this->quick_model->logs(implode(',',$this->input->post("meta_id"))." ids deleted ");
			$result="1";
		}else{
			$result="0";
		}
	   	echo $result;
    	exit;	
    }
    
    function meta_link_order(){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	
    	if($this->input->is_ajax_request()){
    		 
    		$type_ids=explode(',',$this->input->post("type_ids"));
    
    		$i=1;
    		foreach($type_ids as $type_id){
    
    			$this->db->where("m_id",$type_id)->update("meta",array($this->input->post("sort_order")=>$i));
    
    			$i++;
    		}
    		$this->quick_model->logs($this->input->post("sort_order")." link sorted");
    		exit;
    	}
    
    	$this->data["footers"]=$this->quick_model->footer_link()->result_array();
    	$this->data["top_menus"]=$this->quick_model->top_link()->result_array();
    	$this->data["main_menu"]=$this->meta_model->main_menu()->result_array();
    	
    	 
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('meta/meta_order',$this->data);
    }
    
    function plugin_order(){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	
    	if($this->input->is_ajax_request()){
    		foreach (explode(",",$this->input->post("plugins")) as $k=>$page_position_id){
    			
    			$page_position_id=explode("-",$page_position_id);
    			
    			$this->db->where(array("meta_id"=>$this->input->post("m_id"),"plugin_id"=>$page_position_id[0],"page_position_id"=>$page_position_id[1],"type_id"=>$page_position_id[2]))->update("plugin_to_page",array("sort_order"=>$k));
    		}
    	echo 1;exit;
    	}
    }
    
    function top_menu(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("meta_id")>0){
    
    		$this->db->where("meta_id",$this->input->post("meta_id"))->update("meta",array("top_menu"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function footer_menu(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("meta_id")>0){
    
    		$this->db->where("meta_id",$this->input->post("meta_id"))->update("meta",array("footer"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function main_menu(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("m_id")>0){
    
    		$this->db->where("m_id",$this->input->post("m_id"))->update("meta",array("main_menu"=>$status));
    		
    		if($status==1){
    			$this->db->insert("link_to_menu",array("top_m_id"=>0,"sub_m_id"=>$this->input->post("m_id") ));
    		}else{
    			$this->db->where(array("top_m_id"=>0,"sub_m_id"=>$this->input->post("m_id")))->delete("link_to_menu");
    		}
    		
    	}
    
    	echo 1; exit;
    
    }
    
    function get_filter_message(){
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="type"){
				$group=$this->db->where_in("meta_type_id",$value)->get("meta_type")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_meta',"");
	redirect('admin/meta');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$meta_session=$this->sessiondd->userdata('meta') ;
			
				$meta_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('meta',$meta_session);
			
				
				if(	isset($meta_session["orderby_order"]) ){
				
					if($meta_session["orderby_order"]=="asc" ){
					
						$meta_session["orderby_order"]="desc";
					}else{
					
						$meta_session["orderby_order"]="asc";
					}
					
				}else{
				
					$meta_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('meta',$meta_session);
			}
	
	redirect('admin/meta');
	}
	
	function check_slug($link){
		
		if($link=="#"){
			return true;
		}

    	$meta=$this->meta_model->meta(array("link"=>$link));
    	
    	if($meta->num_rows>0){
    	
    	$this->form_validation->set_message('check_slug', 'The meta '.$meta->row()->link.' %s is already in use meta id:'.$meta->row()->meta_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}
	
	function check_slug_edit($link){
		
		$this->db->where("meta_id <>",$this->ids);
    	$meta=$this->meta_model->meta(array("link"=>$link));
    	
    	if($meta->num_rows>0){
    	
    	$this->form_validation->set_message('check_slug_edit', 'The meta '.$meta->row()->link.' %s is already in use meta id:'.$meta->row()->meta_id);
    	return false;
    	}else{
    	
    	return true;
    	}
	
	}
	
	function sync_data($meta_type_id,$data_id,$slug){
	
		if(array_key_exists($meta_type_id,$this->meta_types)){
		
		$this->db->where($this->meta_types[$meta_type_id]."_id",$data_id)->update($this->meta_types[$meta_type_id],array("slug"=>$slug));
		}
	
	}
	
	
	
	function upload($meta_id){
	
		if($_FILES["ImageFile"]["error"][0]!=4){
	
			
			$dirpath="uploads/meta_icon/".$meta_id;
			create_dir($dirpath);
	
			$config['upload_path'] ='./uploads/meta_icon/'.$meta_id.'/';
	
			$config['file_name'] =md5($meta_id);;
			$config['allowed_types'] = 'gif|jpg|jpeg|png|ico';
			$config['max_size']	= '22222100';
			$config['max_width']  = '22222221024';
			$config['max_height']  = '222222768';
	
			$this->load->library('upload', $config);
			$result=$this->quick->upload("ImageFile");

			foreach($result["success"] as $image){
					
				$this->db->where("m_id",$meta_id)->update("meta" ,array("icon"=>'uploads/meta_icon/'.$meta_id.'/'.$image["file_name"]));
					
			}
	
		}
	}	
	
	function delete_image(){
		 
		$this->permission->check_permission("delete");
		
		$image_url=$this->db->where(array("meta_id"=>$this->input->post("meta_id")) )->get("meta");
		$image_url=$image_url->row()->icon;
	
		$this->db->where( array("meta_id"=>$this->input->post("meta_id")) )->update("meta",array("icon"=>""));
	
		unlink($_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
		exit;
	}
	
	# PHP < 5.5
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array();
	
		if (null === $indexKey) {
			if (null === $columnKey) {
				// trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
				$result = array_values($input);
			}
			else {
				foreach ($input as $row) {
					$result[] = $row[$columnKey];
				}
			}
		}
		else {
			if (null === $columnKey) {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row;
				}
			}
			else {
				foreach ($input as $row) {
					$result[$row[$indexKey]] = $row[$columnKey];
				}
			}
		}
	
		return $result;
	}
	
	function download_as_exel(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
	
		$this->load->library("excel");
		$this->load->library("language_lib");
	
		///post1 ///
		if( $this->input->post()){
	
			$this->form_post_meta=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_meta',$this->form_post_meta);
		}else{
	
			$this->form_post_meta=$this->sessiondd->userdata('form_post_meta') ;
		}
		$this->where_work();
		
		$this->download_meta_excel($this->db->get('meta')->result_array());
			
	
	}
	
	
	function download_meta_excel($data_to_excel){
	
		// Set document properties
		$this->objPHPExcel->getProperties()->setCreator("Muslum CEN")
		->setLastModifiedBy("Muslum CEN")
		->setTitle("Cart21 Meta")
		->setSubject("Cart21 Meta")
		->setDescription("Cart21 Meta")
		->setKeywords("cart21 shopping cart ")
		->setCategory("meta");
	
	
		$this->objPHPExcel->setActiveSheetIndex(0)
		
		->setCellValue('A1',"meta id")
		->setCellValue('B1', "m id")
		->setCellValue('C1',"Language ID")
		->setCellValue('D1', "Title")
		->setCellValue('E1', "Link")
		->setCellValue('F1', "CI Routes")
		->setCellValue('G1', "redirect")
		->setCellValue('H1', "Description")
		->setCellValue('I1', "Keywords")
		->setCellValue('J1', "type")
		->setCellValue('K1', "type_id");
	
		$i=2;
		// Add some data
		foreach ($data_to_excel as $l){
			$this->objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$i, $l["meta_id"])
			->setCellValue('B'.$i, $l["m_id"])
			->setCellValue('C'.$i, $l["language_id"])
			->setCellValue('D'.$i, $l["title"])
			->setCellValue('E'.$i, $l["link"])
			->setCellValue('F'.$i, $l["class_routes"])
			->setCellValue('G'.$i, $l["redirect"])
			->setCellValue('H'.$i, $l["description"])
			->setCellValue('I'.$i, $l["keywords"])
			->setCellValue('J'.$i, $l["type"])
			->setCellValue('K'.$i, $l["type_id"]);
	
			$i++;
		}
		// Miscellaneous glyphs, UTF-8
	
		foreach(range('A','F') as $columnID) {
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
			->setWidth(6);
		}
		
		$W=30;
		$this->objPHPExcel->getActiveSheet()->getColumnDimension("D") ->setWidth($W);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension("E") ->setWidth($W);
		$this->objPHPExcel->getActiveSheet()->getColumnDimension("F") ->setWidth($W);
	
		// Rename worksheet
		$this->objPHPExcel->getActiveSheet()->setTitle('meta file');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment;filename="meta.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
	
	function upload_form(){
		
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
		$this->data["page"]="Upload";
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('meta/upload_form',$this->data);
	}
	
	function upload_excel(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("add");
		$this->permission->check_permission("edit");
	
		if(isset($_FILES["excel_upload"])){
			if($_FILES["excel_upload"]["error"][0]!=4){
	
				$config['upload_path'] ='./uploads/excel_meta/';
				
				create_dir("/uploads/excel_meta");
	
				//$config['file_name'] = mktime()+rand(1,5);
				$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php|vnd.ms-office';
				$config['max_size']	= '22222100';
				$config['max_width']  = '22222221024';
				$config['max_height']  = '222222768';
				$config['overwrite']  = true;
					
	
				$this->load->library('upload', $config);
				$result=$this->quick->upload("excel_upload");
					
				if(isset($result["error"])){
					$this->quick->errors=$result["error"];
				}
	
				foreach($result["success"] as $image){
	
					$this->load->library("excel");
						
					$inputFileName = "./uploads/excel_meta/".$image["file_name"];
						
					//  Read your Excel workbook
					try {
						$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
						$objReader = PHPExcel_IOFactory::createReader($inputFileType);
						$objPHPExcel = $objReader->load($inputFileName);
					} catch(Exception $e) {
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
					}
						
					//  Get worksheet dimensions
					$sheet = $objPHPExcel->getSheet(0);
					$highestRow = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
						
					//  Loop through each row of the worksheet in turn
					for ($row = 2; $row <= $highestRow; $row++){
					//  Read a row of data into an array
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
								NULL,
								TRUE,
								FALSE);
							
						$data["meta_id"]=$rowData[0][0];
						$data["m_id"]=$rowData[0][1];
						$data["language_id"]=$rowData[0][2];
						$data["title"]=$rowData[0][3];
						$data["link"]=$rowData[0][4];
						$data["class_routes"]=$rowData[0][5];
						$data["redirect"]=$rowData[0][6];
						$data["description"]=$rowData[0][7];
						$data["keywords"]=$rowData[0][8];
						$data["type"]=$rowData[0][9];
						$data["type_id"]=$rowData[0][10];
						
						if($data["title"]=="" or $data["link"]="" ){continue;}
						$check1=$this->db->where(array("link"=>$data["link"],"type"=>$data["type"],"language_id"=>$data["language_id"]))->get("meta");
						
						if($check1->num_rows){
								
							echo "{($row)} Already exist ".implode(" - ",$data)."<br>";
							
						}else{
						
							$this->db->insert("meta",array_diff_key($data,array("meta_id"=>"")));
							$meta_id=$this->db->insert_id();
							$this->db->where("meta_id",$meta_id)->update("meta",array("m_id"=>$meta_id));
							echo "{($row)} <b>New insertion<b> ".implode(" - ",$data)."<br>";
						}
	
					//  Insert row data array into your database of choice here
					}
						
						echo "<a href='/admin/meta'>All metas</a>";
						
				}
	
			}
		}
	
	}
	

		
}