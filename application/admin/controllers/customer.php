<?php
class customer extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('adminuser_model');
        $this->load->model('order_model');
         
        $this->load->model('user');
       
    }
 
    function get_shape(){
   
     return array(
				"customer_id" => $this->data['L']["id"],
				"email" => $this->data['L']["email"], 
				"firstname" =>$this->data['L']["firstname"],
				"lastname" => $this->data['L']["lastname"],
				"birthday" => $this->data['L']["birthday"],
				"note" => $this->data['L']["note"],
				"telephone" => $this->data['L']["telephone"],
				"date_added" => $this->data['L']["dateadded"],
				"status" => $this->data['L']["status"]
			);
    
    }
    
    function where_work(){
    
			
			///get1 ///
			
			if($this->input->get("customer_id")){
			
			$this->form_post_customer["customer_id"]=$this->input->get("customer_id");
			$this->sessiondd->set_userdata('form_post_customer',$this->form_post_customer);
			}else{
			
			if(isset($this->form_post_customer["customer_id"])){
			unset($this->form_post_customer["customer_id"]);
			}
			
			
			$this->sessiondd->set_userdata('form_post_customer',$this->form_post_customer);
			}
			
	

		if(is_array($this->form_post_customer)){
		
			$this->form_post_customer_where=array_diff_key($this->form_post_customer,$pattern=array(
					"customer_group_id"=>"customer_group_id",
					"firstname"=>"firstname",
					"lastname"=>"lastname"
					
					));
			
			if(isset($this->form_post_customer["customer_group_id"])){
				
				$customer=$this->db->query("select * from customer_to_group where customer_group_id in(".implode(",",$this->form_post_customer["customer_group_id"]).")" );
				
				if($customer->num_rows){
					
					$customer_id=array_column($customer->result_array(),"customer_id");
					
					$this->db->where_in("customer_id",$customer_id);
				}else{
					$this->db->where("customer_id",null);
				}
				
			}
			if(isset($this->form_post_customer["firstname"])){
				$this->db->where("(lower(firstname) like '%".strtolower($this->form_post_product_f["firstname"])."%') ");
			}
			if(isset($this->form_post_customer["lastname"])){
				$this->db->where("(lower(lastname) like '%".strtolower($this->form_post_product_f["lastname"])."%') ");
			}
			
			$this->db->or_like($this->form_post_customer_where);
			
		}else{
		$this->form_post_customer=array();
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
		
			$this->form_post_customer=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_customer',$this->form_post_customer);
		}else{
		
			$this->form_post_customer=$this->sessiondd->userdata('form_post_customer') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_customer;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('customer')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=15;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/customer/index';
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
		
		$customer_session=$this->sessiondd->userdata('customer');
		
		//$this->dbg2($customer_session);
		if (	isset($customer_session["orderby"])		){
		
		$this->db->order_by($customer_session["orderby"], $customer_session["orderby_order"]);
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
 		//$this->dbg2($this->data);
 		
 		$customers=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("customer");   ///segment 4 page
		//$this->dbg2($customers->result_array());
		$this->data["customers"]=$this->modules->customer_list($this->smarty,$customers);
		
		if($action=="list"){
		echo $this->data["customers"];
		exit;
		}
		
		$this->data["customer_group"]=$this->adminuser_model->customer_groups()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('users/customer/customer',$this->data);
	 
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
	
			$this->db->where("customer_id",$this->input->post("customer_id"))->update("customer",array("status"=>$status ));
	
		}
	
		echo 1; exit;
	
	}
	
	function edit($ids){
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	
    	$this->data["page"]="edit";
    	
    	$this->load->helper(array('form', 'url'));
    	$this->load->library('form_validation');
    
    	$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean|valid_email');
    	$this->form_validation->set_rules('firstname', 'firstname', 'trim|required|xss_clean');
    	$this->form_validation->set_rules('lastname', 'lastname', 'trim|required|xss_clean');
    	
    	
    	if( $this->input->post()  and $this->permission->check_permission("edit")){
    		
    		$this->data["POST"]=$this->input->post();
    	
    		if ($this->form_validation->run() && $this->email_exist($this->input->post("email"),$ids) ) { 
    			
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
					   'note' => ($this->input->post("note")),
						'status' =>$this->data["POST"]["status"],
						'birthday' =>strtotime($this->data["POST"]["month"]."/".$this->data["POST"]["day"]."/".$this->data["POST"]["year"]),
							
					   'country_code' => ($this->input->post("country_code"))
					);
					
				if($this->input->post("month") and $this->input->post("day") and $this->input->post("year") ){
					$user['birthday']=mktime(0,0,0,$this->input->post("month"),$this->input->post("day"),$this->input->post("year"));
				}
				
				$this->db->where('customer_id', $ids);
				$this->db->update('customer', $user);
				///
				$this->db->delete('customer_to_group', array('customer_id' => $ids)); 
				if($this->input->post("customer_group_id")){
				foreach ($this->input->post("customer_group_id")  as $g_id){
					$this->db->insert('customer_to_group', array("customer_id" => $ids,"customer_group_id"=>$g_id) );
				}
				}
				///
				$this->quick->success[]=" Successfully Edited ";
				$this->quick_model->logs("customer  "+$this->input->post("firstname")+" Successfully Edited");
			}else{
			
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
				
			}	
    	}
    	
    	
    	if( is_array($ids) ){
		$ids=implode(',',$ids);		
		}
	
    	$user=$this->user->getUser($ids);
    	
		
    	$this->data["POST"]=$user[0];
    	
    	$birthday=$this->quick->set_date2($user[0]["birthday"]);
    	$this->data["POST"]["month"]=$birthday["mon"];
    	$this->data["POST"]["year"]=$birthday["year"];
    	$this->data["POST"]["day"]=$birthday["mday"];
    	
    	$this->data["customer_group"]=$this->adminuser_model->customer_groups()->result_array();
    	$this->data["customer_groups"]=array_column($this->db->where('customer_id', $ids)->get("customer_to_group")->result_array(),"customer_group_id");
		
    	$this->data["countries"]=$this->order_model->get_Countries()->result_array();
    	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	
		
	
	$this->smarty->view('users/customer/customer_form',$this->data);
	}
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("customer");
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("customer_to_group");
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("order");
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("order_product");
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("product_comment");
			$this->db->where_in("customer_id",$this->input->post("customer_id"))->delete("support");
			
			$this->quick_model->logs(implode(',',$this->input->post("customer_id"))." idli customerlar silindi ");
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
		
			if($key=="customer_group_id"){
				$group=$this->db->where_in("customer_group_id",$value)->get("customer_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"customer_group_name") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_customer',"");
	redirect('admin/customer');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$customer_session=$this->sessiondd->userdata('customer') ;
			
				$customer_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('customer',$customer_session);
			
				
				if(	isset($customer_session["orderby_order"]) ){
				
					if($customer_session["orderby_order"]=="asc" ){
					
						$customer_session["orderby_order"]="desc";
					}else{
					
						$customer_session["orderby_order"]="asc";
					}
					
				}else{
				
					$customer_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('customer',$customer_session);
			}
	
	redirect('admin/customer');
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
	
	
	
		
}