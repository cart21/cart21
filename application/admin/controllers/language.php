<?php
class language extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('language_model');
        $this->load->model('meta_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"language_id" =>$this->data["L"]["id"],
    			"name" => $this->data["L"]["languagename"],
    			"short_name" => $this->data["L"]["languagecode"],
    			"status" => $this->data["L"]["status"],
    			"image" =>$this->data["L"]["image"]

			);
    
    }
    
    function where_work(){
    
			
		
		if(is_array($this->form_post_language)){
		
			$this->form_post_language_where=array_diff_key($this->form_post_language,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_language_where);
			
			
			if(isset($this->form_post_language["link"])){
				$this->db->like("link",$this->form_post_language["link"]);
			}
			
			if(isset($this->form_post_language["class_routes"])){
				$this->db->like("class_routes",$this->form_post_language["class_routes"]);
			}
			
		}else{
		$this->form_post_language=array();
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
		
			$this->form_post_language=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_language',$this->form_post_language);
		}else{
		
			$this->form_post_language=$this->sessiondd->userdata('form_post_language') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_language;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('language')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/language/index';
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
		
		$config['cur_tag_open'] = ' <li><a href="#" class="bg-navy text-white"> <span class="sr-only">';
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
		
		$language_session=$this->sessiondd->userdata('language');
		
		if (	isset($language_session["orderby"])		){
		
		$this->db->order_by($language_session["orderby"], $language_session["orderby_order"]);
		}else{
		$this->db->order_by("language_id", "desc");
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
 		
 		$languages=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("language");   ///segment 4 page
		$this->data["languages"]=$this->modules->language_list($this->smarty,$languages);
		
		if($action=="list"){
		echo $this->data["languages"];
		exit;
		}
			
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('language/language',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('short_name', 'Code', 'trim|required|xss_clean');	
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data["name"]=$this->input->post("name");
	 		$data["short_name"]=$this->input->post("short_name");
	 		
	 		$this->db->insert("language",$data);
	 		
	 		$ids=$this->db->insert_id() ;
	 		
	 		// create static meta
	 		$this->meta_model->create_new_meta_static();
	 		
	 		foreach ($this->language_model->language_c(array("language_id"=>$this->data["settings"]["front_language"]))->result_array() as $l){
	 			
	 			$data_insert["language_id"]=$ids;
	 			$data_insert["key_val"]=$l["key_val"];
	 			$data_insert["text_val"]="{".$l["key_val"]."}";
	 			$this->db->insert("language_c",$data_insert);
	 			
	 			$c_ids=$this->db->insert_id();
	 			
	 			$language_to_page=$this->language_model->language_to_page($l["language_c_id"]);
	 			if($language_to_page->num_rows){
		 			
	 				foreach ($language_to_page->result_array() as $assign ){
	 					
	 					$this->db->insert("language_c_to_page", array("meta_id"=>$assign["meta_id"],"language_c_id"=>$c_ids));
		 			}
	 			}
	 			
	 		}
	 		
	 		$this->upload($ids);
	 		
	 		
	 		
	 		
	 		$this->quick_model->logs($ids." id language added  ");
	 		
	 	redirect("admin/language");
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
	$this->smarty->view('language/language_form',$this->data);
	
    }
    
    function edit($ids){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
	$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("edit") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('short_name', 'Code', 'trim|required|xss_clean');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data["name"]=$this->input->post("name");
	 		$data["short_name"]=$this->input->post("short_name");
	 		
	 		$this->db->where("language_id",$ids)->update("language",$data);
	 		
	 		$this->upload($ids);
	 		$this->upload_excel($ids);
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		
		$this->data["POST"]=$this->db->where("language_id",$ids)->get("language")->row_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('language/language_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			// english default language canot be deleted 
			if(in_array(1,$this->input->post("language_id"))){
				exit;
			}
			$this->db->where_in("language_id",$this->input->post("language_id"))->delete("language");

			$this->db->where_in("language_id",$this->input->post("language_id"))->delete("language_c");
			
			$this->quick_model->logs(implode(',',$this->input->post("language_id"))." idli languagelar silindi ");
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
		
			if($key=="language_group"){
				$group=$this->db->where_in("language_group_id",$value)->get("language_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_language',"");
	redirect('admin/language');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$language_session=$this->sessiondd->userdata('language') ;
			
				$language_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('language',$language_session);
			
				
				if(	isset($language_session["orderby_order"]) ){
				
					if($language_session["orderby_order"]=="asc" ){
					
						$language_session["orderby_order"]="desc";
					}else{
					
						$language_session["orderby_order"]="asc";
					}
					
				}else{
				
					$language_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('language',$language_session);
			}
	
	redirect('admin/language');
	}
	
	function upload($language_id){
	
		if($_FILES["ImageFile"]["error"][0]!=4){
	
			$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/language/";
	
			if (!file_exists($directoryPath)) {
				mkdir($directoryPath, 0755);
			}
	
			$config['upload_path'] ='./uploads/language/';
	
			$config['file_name'] = mktime()+rand(1,5);
			$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
			$config['max_size']	= '22222100';
			$config['max_width']  = '22222221024';
			$config['max_height']  = '222222768';
	
			$this->load->library('upload', $config);
			$result=$this->quick->upload("ImageFile");

			foreach($result["success"] as $image){
					
				$this->db->where("language_id",$language_id)->update("language" ,array("image"=>'uploads/language/'.$image["file_name"]));
					
			}
	
		}
	}
	
	function change_status(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
	
		$result["status"]=null;
		/// control ///
		if( ($this->data["settings"]["admin_language"]==$this->input->post("language_id") or $this->data["settings"]["front_language"]==$this->input->post("language_id") )){
		
			$result["message"]=" It is setting language status cannot be changed ";
			echo json_encode($result); exit;
		}
		/// control ///
		
		if($this->input->post("status")=="true"){
	
			$result["status"]=1;
			$this->new_activate($this->input->post("language_id"));
		}else {
	
			$result["status"]=0;
			
			$this->db->query("delete  from language_c_to_page where language_c_id in( select language_c_id from language_c where language_id=".$this->input->post("language_id").")");
			$this->db->query("delete  from language_c_to_section where language_c_id in( select language_c_id from language_c where language_id=".$this->input->post("language_id").")");
		}
	
		if($this->input->post("language_id")>0){
	
			$this->db->where("language_id",$this->input->post("language_id"))->update("language",array("status"=>$result["status"] ));
		}
		echo json_encode($result); exit;
	}
	
	function new_activate($id){
	
		$sql="select L1.key_val,  L1.language_c_id from  (SELECT * FROM `language_c` WHERE `language_id`in(".$this->data["settings"]["front_language"].",".$this->data["settings"]["admin_language"].") ) as L1
	
			left join (SELECT * FROM `language_c` WHERE `language_id`=".$id." ) as L2
			on L2.`key_val`=L1.`key_val`
		
			where L2.`key_val` is null  group by L1.`key_val` ";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
				
			foreach($result->result_array() as $l){
			
				$new_data["key_val"]=$l["key_val"];
				$new_data["text_val"]="{".$l["key_val"]."}";
				$new_data["language_id"]=$id;
	
				$this->db->insert("language_c",$new_data);
				
			}
				
		}
	
		
		$this->update_relation($id);
	
	}

	function update_relation($language_id){
		
		$sql="replace into language_c_to_section 
		SELECT  ls.section_id , new_lang.language_c_id  FROM (SELECT * FROM `language_c`where `language_id` in(".$this->data["settings"]["admin_language"].")  ) as l
		
		
		left join ( SELECT * FROM `language_c`where `language_id` in(".$language_id.") ) as new_lang 
		on new_lang.`key_val`=l.`key_val`
		
		inner join language_c_to_section as ls
		on ls.language_c_id=l.language_c_id";
		
		$this->db->query($sql);
		
		$sql="replace into language_c_to_page
		SELECT  ltp.meta_id , new_lang.language_c_id  FROM (SELECT * FROM `language_c`where `language_id` in(".$this->data["settings"]["front_language"].")  ) as l
		
		
		left join ( SELECT * FROM `language_c`where `language_id` in(".$language_id.") ) as new_lang
		on new_lang.`key_val`=l.`key_val`
		
		inner join language_c_to_page as ltp
		on ltp.language_c_id=l.language_c_id";
		
		$this->db->query($sql);
		
	}
	
	function download_excel($language_id){
		
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
		
		//$language=$this->language_model->language(array("language_id"=>$language_id));
		
		$this->db->order_by("text_val","asc");
		$language_c=$this->language_model->language_c(array("language_id"=>$language_id));
		
		$this->load->library("excel");
		$this->load->library("language_lib");
		
		$this->language_lib->download($language_c->result_array());
		
	}
	
	function upload_excel($language_id){
		
		$this->permission->check_permission("view");
		$this->permission->check_permission("add");
		$this->permission->check_permission("edit");
		
		if(isset($_FILES["excel_upload"])){
			if($_FILES["excel_upload"]["error"][0]!=4){
		
				$config['upload_path'] ='./uploads/excel_language/';
		
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
					
					$inputFileName = "./uploads/excel_language/".$image["file_name"];
					
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
							
						$data["language_id"]=$language_id;
						$data["key_val"]=$rowData[0][0];
						$data["text_val"]=$rowData[0][1];
							
						$check=$this->db->where(array("key_val"=>$data["key_val"],"language_id"=>$data["language_id"]))->get("language_c");
							
						if($check->num_rows){
							
							$this->db->where("language_c_id",$check->row()->language_c_id)->update("language_c",$data);
							$language_c_id=$check->row()->language_c_id;
						}else{
							$this->db->insert("language_c",$data);
							$language_c_id=$this->db->insert_id();
						}
						
						/// page assign ///
						$lucy_page=explode(",",$rowData[0][2]);
						if(count(array_filter($lucy_page))>0){
						foreach ($lucy_page as $page){
							$this->db->query("replace into language_c_to_page (meta_id, language_c_id) values ( ".$page." , ".$language_c_id." ) ");
						}
						}
						
						/// page assign ///
						
						/// section assign ///
						$lucy_section=explode(",",$rowData[0][3]);
						if(count(array_filter($lucy_section))>0){
						foreach ($lucy_section as $section){
							$this->db->query("replace into language_c_to_section (section_id, language_c_id) values ( ".$section." , ".$language_c_id." ) ");
						}
						}
						/// section assign ///
						
						
						
						//  Insert row data array into your database of choice here
					}
					
					
					
				}
		
			}
		}
		
	}
	
	
}