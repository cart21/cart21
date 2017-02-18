<?php
class useradmin extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        
        $this->load->model('user');
        $this->load->model('adminuser_model');
        $this->load->model('order_model');
       
    }
 	
 	function get_shape(){
   
    return array(
    		"customer_id" => "Customer_id",
    		"email" => "E-mail",
    		"firstname" =>"Firstame",
    		"lastname" => "Lastname",
    		"birthday" => "birthday",
    		"telephone" => "telephone",
    		"fax" => "fax",
    		"date_added" => "date_added",
    		"status" => "status"
			);
    
    }
    
    function where_work(){
    
			
			///get1 ///
			
			if($this->input->get("customer_id")){
			
			$this->form_post_admin["customer_id"]=$this->input->get("customer_id");
			$this->sessiondd->set_userdata('form_post_admin',$this->form_post_admin);
			}else{
			
			if(isset($this->form_post_admin["customer_id"])){
			unset($this->form_post_admin["customer_id"]);
			}
			$this->sessiondd->set_userdata('form_post_admin',$this->form_post_admin);
			}
			
			///get1 *///

		if(is_array($this->form_post_admin)){
		
			$this->form_post_admin_where=array_diff_key($this->form_post_admin,$pattern=array(
					"admin_group_id"=>"admin_group_id",
					
					));
			$this->db->or_like($this->form_post_admin_where);
			
			
			if(isset($this->form_post_admin["admin_group_id"])){
			
				$admin=$this->db->query("select * from admin_to_group where admin_group_id in(".implode(",",$this->form_post_admin["admin_group_id"]).")" );
			
				if($admin->num_rows){
						
					$admin_id=array_column($admin->result_array(),"admin_id");
						
					$this->db->where_in("customer_id",$admin_id);
				}else{
					$this->db->where("customer_id",null);
				}
			
			}
			
		}else{
		$this->form_post_admin=array();
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
		
			$this->form_post_admin=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_admin',$this->form_post_admin);
		}else{
		
			$this->form_post_admin=$this->sessiondd->userdata('form_post_admin') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_admin;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('admin')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=15;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/useradmin/index';
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
		
		$admin_session=$this->sessiondd->userdata('admin_session');
		
		//$this->dbg2($admin_session);
		if (	isset($admin_session["orderby"])		){
		
		$this->db->order_by($admin_session["orderby"], $admin_session["orderby_order"]);
		}else{
		$this->db->order_by("customer_id", "desc");
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
 		
 		
 		$users=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("admin");   ///segment 4 page
		$this->data["useradmins"]=$this->modules->admin_list($this->smarty,$users);
		
		if($action=="list"){
		echo $this->data["useradmins"];
		exit;
		}
		
	
		$this->data["admin_group"]=$this->adminuser_model->admin_groups()->result_array();
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('users/admin',$this->data);
	 
	}
    
    function delete(){
  	
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("admin");
			$this->db->where_in("admin_id",$this->input->post("customer_id"))->delete("admin_to_group");
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
    
    	//dbg($this->input->post());
    
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    
    	if($this->input->post("customer_id")>0){
    
    		$this->db->where("customer_id",$this->input->post("customer_id"))->update("admin",array("status"=>$status ));
    
    	}
    
    	echo 1; exit;
    
    }
    
    function edit($ids){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	$this->data["page"]="edit";
    	
    	$this->load->library('form_validation');
    	$this->form_validation->set_rules('firstname', 'firstname', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('lastname', 'lastname ', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'email ', 'trim|required|xss_clean');
		$this->form_validation->set_rules('admin_group_id', 'Admin group', 'required|xss_clean');
		
		if($this->input->post("password")){
			
			$this->form_validation->set_rules('password', 'password ', 'trim|required|xss_clean|matches[repassword]');
		}
		
		if( $this->input->post() and $this->permission->check_permission("add") ){
    	
			$this->data["POST"]=$this->input->post();
			
    		if ($this->form_validation->run() 	) { 
    	
    			///
    			if($this->input->post("status")=="on"){
    				 
    				$this->data["POST"]["status"]=1;
    			}else {
    				 
    				$this->data["POST"]["status"]=0;
    			}
    			///
    			$user = array(
    					'firstname' => ($this->input->post("firstname")),
    					'lastname' => ($this->input->post("lastname")),
    					'email' =>  ($this->input->post("email")),
    					'telephone' => ($this->input->post("telephone")),
    					'status' =>$this->data["POST"]["status"],
    					'birthday' =>strtotime($this->data["POST"]["month"]."/".$this->data["POST"]["day"]."/".$this->data["POST"]["year"]),
    					 
    					'country_code' => ($this->input->post("country_code"))
    			);
    			
    		
    			
				if($this->input->post("password")){
				$user["password"]=sha1($this->input->post("password"));
				}
				$this->db->where('customer_id', $ids);
				$this->db->update('admin', $user);
		
				///
				$this->db->delete('admin_to_group', array('admin_id' => $ids));
				if($this->input->post("admin_group_id")){
					foreach ($this->input->post("admin_group_id")  as $g_id){
						$this->db->insert('admin_to_group', array("admin_id" => $ids,"admin_group_id"=>$g_id) );
					}
				}
				///
				$this->upload($ids);
				
				$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
				
				$this->quick_model->logs("admin  ".$this->input->post("firstname")." Successfully Edited");
				
	  		}else {
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
			}
    	}
    	
    	
    if( is_array($ids) ){
		$ids=implode(',',$ids);		
	}
	
    	$user=$this->adminuser_model->getUser($ids);
    	$this->data["POST"]=$user[0];
    	 
    	$birthday=$this->quick->set_date2($user[0]["birthday"]);
    	$this->data["POST"]["month"]=$birthday["mon"];
    	$this->data["POST"]["year"]=$birthday["year"];
    	$this->data["POST"]["day"]=$birthday["mday"];
    	
    	
    	
    	$this->data["admin_groups"]=$this->adminuser_model->admin_groups()->result_array();
    	
    	
    	//
    	$this->data["lucky_admin_groups"]=array_column($this->db->where('admin_id', $ids)->get("admin_to_group")->result_array(),"admin_group_id");
		//
		
    	$this->data["countries"]=$this->order_model->get_Countries()->result_array(); 
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	
		
	
	$this->smarty->view('users/admin_form',$this->data);
	
	
    }
    
    function add(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("add");
    	$this->data["page"]="add";
    	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		
		
		$this->form_validation->set_rules('firstname', 'isim', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('lastname', 'soyisim ', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'password ', 'trim|required|xss_clean|matches[repassword]');
		$this->form_validation->set_rules('email', 'e posta ', 'trim|required|xss_clean');
		$this->form_validation->set_rules('admin_group_id', 'Admin group', 'required|xss_clean');
		
		if( $this->input->post() and $this->permission->check_permission("add") ){
    	
			$this->data["POST"]=$this->input->post();
				
			if ($this->form_validation->run() 	) {
				 
				///
				if($this->input->post("status")=="on"){
						
					$this->data["POST"]["status"]=1;
				}else {
						
					$this->data["POST"]["status"]=0;
				}
				///
				$user = array(
						'firstname' => ($this->input->post("firstname")),
						'lastname' => ($this->input->post("lastname")),
						'email' =>  ($this->input->post("email")),
						'telephone' => ($this->input->post("telephone")),
						'status' =>$this->data["POST"]["status"],
						'birthday' =>strtotime($this->data["POST"]["month"]."/".$this->data["POST"]["day"]."/".$this->data["POST"]["year"]),
					'country_code' => ($this->input->post("country_code"))
				);
					
				if($this->input->post("password")){
				$user["password"]=sha1($this->input->post("password"));
				}
					
				//$this->quick->dbg($user);  
		 
				$this->db->insert('admin', $user);
				$ids=$this->db->insert_id() ;
				
				///
				
				if($this->input->post("admin_group_id")){
					foreach ($this->input->post("admin_group_id")  as $g_id){
						$this->db->insert('admin_to_group', array("admin_id" => $ids,"admin_group_id"=>$g_id) );
					}
				}
				///
				redirect('admin/useradmin');
			}else {
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
		}
	
	}
    	
    $this->data["POST"]=$this->input->post();
    	
    
    $this->data["admin_groups"]=$this->adminuser_model->admin_groups()->result_array();
    $this->data["lucky_admin_groups"]=array();
    $this->data["countries"]=$this->order_model->get_Countries()->result_array(); 
	$this->data["action"]="";
	
	$this->quick->Header("");
	$this->quick->Top_menu("");
	$this->quick->Footer("");
	
	$this->smarty->view('users/admin_form',$this->data);
	
    }
    
    function email_exist($email,$id){
    	
    	$customer=$this->db->where("email",$email)->where("customer_id <>",$id)->get("customer");
    	
    	if($customer->num_rows==0){
    	return true;
    	}else{
    	 $this->quick->errors[]=$this->input->post("email")." Bu e-posta adresi daha önce kayıtlı ";
    	return false;
    	}
    
    }
    
    function upload($admin_id){
    
    	if($_FILES["ImageFile"]["error"][0]!=4){
    
    		$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/admin/";
    
    		if (!file_exists($directoryPath)) {
    			mkdir($directoryPath, 0755);
    		}
    
    		$config['upload_path'] ='./uploads/admin/';
    		 
    		$config['file_name'] = mktime()+rand(1,5);
    		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
    		$config['max_size']	= '22222100';
    		$config['max_width']  = '22222221024';
    		$config['max_height']  = '222222768';
    
    		$this->load->library('upload', $config);
    		$result=$this->quick->upload("ImageFile");
   // dbg($result);
      		
    		foreach($result["success"] as $image){
    				
    		$this->db->where("customer_id",$admin_id)->update("admin" ,array("image_url"=>'uploads/admin/'.$image["file_name"]));	
    		}
    
    	}
    }
    
    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="customer_id"){
				$customer=$this->db->where("customer_id",$value)->get("customer")->row_array();
				$message.= "<p>".$this->shape[$key].":".$customer["firstname"]." ".$customer["lastname"]." </p>";
			}elseif($key=="admin_group_id"){
				$group=$this->db->where_in("admin_group_id",$value)->get("admin_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_admin',"");
	redirect('admin/useradmin');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$admin_session=$this->sessiondd->userdata('admin_session') ;
			
				$admin_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('admin_session',$admin_session);
			
				if(	isset($admin_session["orderby_order"]) ){
				
					if($admin_session["orderby_order"]=="asc" ){
					
						$admin_session["orderby_order"]="desc";
					}else{
					
						$admin_session["orderby_order"]="asc";
					}
					
				}else{
				
					$admin_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('admin_session',$admin_session);
			}
	
	redirect('admin/rezervation');
	}
    

    
     
		
}