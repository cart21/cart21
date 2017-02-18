<?php
class plugin extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        $this->load->library('page_position_lib');
        
    }
 
    function get_shape(){
   
    return array(
    			
    			"plugin_id" => $this->data["L"]["id"],
    			"title" => $this->data["L"]["title"],
    			"plugin_type_id" => $this->data["L"]["plugintype"],
    			"publisher" => $this->data["L"]["publisher"],
    			"status" => $this->data["L"]["status"],
    			"content" => $this->data["L"]["content"],
    			"manage_link" => $this->data["L"]["management"],
    			"p_key" => $this->data["L"]["plugin_key"]
    			
			);
    
    }
    
    function where_work(){
    
		if(is_array($this->form_post_plugin)){
		
			$this->form_post_plugin_where=array_diff_key($this->form_post_plugin,$pattern=array("link"=>"link","class_routes"=>"class_routes","title"=>"title"));
			$this->db->where($this->form_post_plugin_where);
			
			
			if(isset($this->form_post_plugin["link"])){
				$this->db->like("link",$this->form_post_plugin["link"]);
			}

			if(isset($this->form_post_plugin["class_routes"])){
				$this->db->like("class_routes",$this->form_post_plugin["class_routes"]);
			}
			if(isset($this->form_post_plugin["title"])){
				$this->db->like("lower(title)",strtolower($this->form_post_plugin["title"]));
			}
			
		}else{
		$this->form_post_plugin=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){
	
		$this->permission->check_permission("view");
		
		$this->quick->Header("");
	
		$this->data["plugin_types"]=$this->plugin_model->plugin_types_arr();
		
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_plugin=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_plugin',$this->form_post_plugin);
		}else{
		
			$this->form_post_plugin=$this->sessiondd->userdata('form_post_plugin') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_plugin;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('plugin')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/plugin/index';
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
		
		$plugin_session=$this->sessiondd->userdata('plugin');
		
		if (	isset($plugin_session["orderby"])		){
		
		$this->db->order_by($plugin_session["orderby"], $plugin_session["orderby_order"]);
		}else{
		$this->db->order_by("plugin_id", "desc");
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
 		
 		
 		$plugins=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("plugin");   ///segment 4 page
	
		$this->data["plugins"]=$this->modules->plugin_list($plugins);
		
		if($action=="list"){
		echo $this->data["plugins"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('plugins/plugin',$this->data);
	 
	}
	
	function upload (){
		
		$this->permission->check_permission("view");
		
		$this->quick->Header("");
		
		if(isset($_FILES["plugin"])){
		if($_FILES["plugin"]["error"][0]!=4){
		
			$config['upload_path'] ='./uploads/plugins/';
			 
			//$config['file_name'] = mktime()+rand(1,5);
			$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
			$config['max_size']	= '22222100';
			$config['max_width']  = '22222221024';
			$config['max_height']  = '222222768';
			$config['overwrite']  = true;
			
		
			$this->load->library('upload', $config);
			$result=$this->quick->upload("plugin");
		
			foreach($result["success"] as $image){
				
				/// control files ///
				
				/// extract to document root ////
			
				$zip = new ZipArchive;
				$open_res=$zip->open("uploads/plugins/".$image["file_name"]);
				if ($open_res === TRUE) {
					$zip->extractTo($_SERVER["DOCUMENT_ROOT"]);
					$zip->close();
					unlink($_SERVER["DOCUMENT_ROOT"]."/uploads/plugins/".$image["file_name"]);
					redirect("/admin/plugins/".str_replace(".zip","",$image["file_name"]));
					
					//echo 'ok';
				} else {
					//echo 'failed';
					switch($open_res){
						case ZipArchive::ER_EXISTS:
							$ErrMsg = "File already exists.";
							break;
					
						case ZipArchive::ER_INCONS:
							$ErrMsg = "Zip archive inconsistent.";
							break;
					
						case ZipArchive::ER_MEMORY:
							$ErrMsg = "Malloc failure.";
							break;
					
						case ZipArchive::ER_NOENT:
							$ErrMsg = "No such file.";
							break;
					
						case ZipArchive::ER_NOZIP:
							$ErrMsg = "Not a zip archive.";
							break;
					
						case ZipArchive::ER_OPEN:
							$ErrMsg = "Can't open file.";
							break;
					
						case ZipArchive::ER_READ:
							$ErrMsg = "Read error.";
							break;
					
						case ZipArchive::ER_SEEK:
							$ErrMsg = "Seek error.";
							break;
					
						default:
							$ErrMsg = "Unknow (Code $rOpen)";
							break;
					
					
					}
					die( 'ZipArchive Error: ' . $ErrMsg);
				}
				
				
					/*
				$image_data=array(
						"image_loc"=>'uploads/product/'.$product_id.'/'.$image["file_name"],
						"product_id"=>$product_id,
						"title"=>str_replace($image["file_ext"],"",$image["client_name"]),
						"file_ext"=>substr($image["file_ext"],1),
						"date_added"=>mktime()
				);
				*/
				//$this->db->insert("product_image",	$image_data	);
					
			}
		
		}
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
		$this->smarty->view('plugins/plugin_upload',$this->data);
	}

    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	$plugin=$this->db->where("plugin_id",$this->input->post("plugin_id"))->get("plugin")->row_array();
    	
    	$this->load->library($plugin["p_key"]);
    	
    	if($status==1){
    		$result=call_user_func_array(array($this->{$plugin["p_key"]},"install"),array("install"));
    	}else{
    		$result=call_user_func_array(array($this->{$plugin["p_key"]},"install"),array("uninstall"));
    	}
    	
    	
    	if($this->input->post("plugin_id")>0 and $result["result"]){
    	
    		$this->db->where("plugin_id",$this->input->post("plugin_id"))->update("plugin",array("status"=>$status ));
    	}
    	
    	echo json_encode($result); exit;
    	 
    }    
    
    function edit($ids){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
		$this->data["POST"]=$this->input->post();
		
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');	
		
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 			$this->data["POST"]["param"]=serialize($this->data["POST"]["param"]);
	 		
	 		$data_plugin=array_diff_key($this->data["POST"],array("testany"=>1,"plugin_to_page"=>1));
	 		
	 		$this->db->where("plugin_id",$ids)->update("plugin",$data_plugin);
	 		
	 		///plugin dynamic to page ///
	 		$this->page_position_lib->after_post(array("type_id"=>0,"plugin_id"=>$ids));
	 		///plugin dynamic to page ///
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->plugin_model->plugin($ids)->row_array();
		
		if($this->data["POST"]["param"]){
			$this->data["POST"]["param"]=unserialize($this->data["POST"]["param"]);
		}
		
		
		$this->data["language_page"]=$this->language_model->language_page()->result_array();
		
		$this->quick->Header("");
		
		$this->page_position_lib->set_page_position_form(array("type_id"=>0,"plugin_id"=>$ids));
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('plugins/plugin_form',$this->data);
    
	
    }
    
    function delete(){
    
    	$result["result"]=true;
		if($this->permission->check_permission("delete") ){
			
			foreach($this->input->post("plugin_id") as $plugin_id){
			
				$plugin=$this->plugin_model->plugin($plugin_id);
				
				if($plugin->num_rows){
					
					if($plugin->row()->status==0){
						$this->plugin_model->delete_plugin($plugin_id);
					}else{
						$result["result"]=false;
						$result["error_message"][]=$plugin->row()->title." cannot be deleted please close it then try again ";
					}
				}
				
			}
		
			
		}else{
			$result["result"]=false;
			$result["error_message"][]=$this->language_model->language_c_key("admintext1");
		}
	   	echo json_encode($result);
    	exit;	
    }
    
    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="plugin_group"){
				$group=$this->db->where_in("plugin_group_id",$value)->get("plugin_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_plugin',"");
	redirect('admin/plugin');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$plugin_session=$this->sessiondd->userdata('plugin') ;
			
				$plugin_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('plugin',$plugin_session);
			
				
				if(	isset($plugin_session["orderby_order"]) ){
				
					if($plugin_session["orderby_order"]=="asc" ){
					
						$plugin_session["orderby_order"]="desc";
					}else{
					
						$plugin_session["orderby_order"]="asc";
					}
					
				}else{
				
					$plugin_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('plugin',$plugin_session);
			}
	
	redirect('admin/plugin');
	}

    function delete_image(){
   
    $image_url=$this->db->where(array("plugin_image_id"=>$this->input->post("file_id")) )->get("plugin_image");
    $image_url=$image_url->row()->image_loc;
    
    $this->db->delete("plugin_image",array("plugin_image_id"=>$this->input->post("file_id")));
    
    unlink($_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
    exit;
    }
    
	function modify_file($modification,$action){

		if($action!="install"){
			$modification=array_reverse($modification);
		}
		
		$control=$this->installation_controll($modification,$action);
		
		if( $control["result"] and count($modification)>0){
			
			foreach ($modification as $file){
		
		    $count = 0;
		    $content=file_get_contents($file["filename"]);
		    
		    $find="";
		    $replacement="";
		 	if($action=="install"){
		 		/// install ///
		 		$find=$file["find"];
		 			
		 		if($file["type"]=="after"){

		 			$replacement=$file["find"].$file["plugin"];
		 			
		 		}elseif($file["type"]=="before"){
		 			
		 			$replacement=$file["plugin"].$file["find"];
		 		}else{
		 			$replacement=$file["plugin"];
		 		}
		    	
		 	}else{
		 		/// uninstall ///
		 		$find=$file["plugin"];
		 		
		 		if($file["type"]=="after"){
		 		
		 			$replacement="";
		 		
		 		}elseif($file["type"]=="before"){
		 		
		 			$replacement="";
		 		}else{
		 			
		 			$replacement=$file["find"];
		 		}
			 }
			 
			 $new_content=preg_replace('^'.preg_quote($find).'^' , $replacement, $content,1) ;
			 file_put_contents($file["filename"], $new_content);
			
			}//each
		
		}else{
			//dbg($control["error_message"]);
			//exit;
		}
		
		return $control;
	       
	}
	
	function installation_controll($modification,$action){
		
		$result["result"]=true;
		
		if(count($modification)>0){
			foreach ($modification as $key=>$file){
				
				if(! is_writable($file["filename"]) ){
					
					$result["result"]=false;
					$result["error_message"][]="file is not writeable ".$file["filename"];
				continue;
				}
				
				//
				$content=file_get_contents($file["filename"]);
				
				if ($action=="install"){
					
					if(! preg_match('^'.preg_quote($file["find"]).'^', $content)){
						
						$result["result"]=false;
						$result["error_message"][]="plugin modification not found in ".$file["filename"]."<br>".htmlspecialchars($file["find"]);
					}
				}else{
					
					if(! preg_match('^'.preg_quote($file["plugin"]).'^', $content)){
							
						$result["result"]=false;
						$result["error_message"][]="plugin modification not found in ".$file["filename"]."<br>".htmlspecialchars($file["plugin"]);
					}
				}
				
			}
		}
		return $result;
	}
    
	function dd(){
		
		
		exit;
		
		$count = 0;
		$filename1 = 'application/front/views/templates/account/login.tpl';
		//$filename1 = 'application/admin/controllers/product.php';
		
		
		$modification=array(
				 
		"replacement"	=>	'<input type="password"  name="re-password" class="form-control" placeholder="{$L.repassword}" minlength=5 required />
	                                        </div>
	                                    </div>',
		
		"find"	=>	'
		/// plugin action ///
				 <input type="password"  name="re-password" class="form-control" placeholder="{$L.repassword}" minlength=5 required />
	                                        </div>
	                                    </div>
				
				{if $ci->plugin_model->plugin_key_staus("recaptchalib")}
	                                    <div class="form-group">
											<label class="col-sm-4 control-label" >{$L.securitycode}</label>
											<div class="col-md-4">  {$recaptcha}     </div>
	                                    
	                                    </div>
	                                    {/if}
		/// plugin action ///
		'
					
		);
		
		$this->modify_file($filename1,$modification);
		
		exit;
		$content=file_get_contents($filename1);
		 
		//dbg( $content); exit;
		$new_content=preg_replace('/'.preg_quote($find).'/' , $find."
		
 			ddd fdd(){ }", $content) ;
		echo $new_content;
		//file_put_contents("sitevisitors.txt", $new_content );
		echo $count;
		exit;
		
		
		
		exit;
		
		$dd["recaptcha"]=array(
			"publickey"=>"6Ld9G-wSAAAAAFnqhl0dOjLp20wNq9RhlYLmxUo-",
			"privatekey"=>"6Ld9G-wSAAAAAAFuj_6_BMwfd840NMALeu_rAbqV",
		);
		
		dbg($dd);
		echo serialize($dd);
		
	}
			
}