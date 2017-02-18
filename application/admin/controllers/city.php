<?php
class city extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('order_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"country_code" => $this->data["L"]["country"]." ".$this->data["L"]["code"],
    			"city_code" => $this->data["L"]["code"],
    			"city_name" => $this->data["L"]["name"]
    			
			);
    
    }
    
    function where_work(){
    
		if(is_array($this->form_post_city)){
		
			$this->form_post_city_where=array_diff_key($this->form_post_city,$pattern=array("name"=>"name"));
			$this->db->where($this->form_post_city_where);
			
			
			if(isset($this->form_post_city["name"])){
				
				$this->db->where("( lower(city_name) like '%".strtolower($this->form_post_city["name"])."%' or lower(city_code) like '%".strtolower($this->form_post_city["name"])."%' )");
				
			}
			
			
			
		}else{
		$this->form_post_city=array();
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
		
			$this->form_post_city=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_city',$this->form_post_city);
		}else{
		
			$this->form_post_city=$this->sessiondd->userdata('form_post_city') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_city;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('city_list')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/city/index';
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
		
		$city_session=$this->sessiondd->userdata('city');
		
		if (	isset($city_session["orderby"])		){
		
		$this->db->order_by($city_session["orderby"], $city_session["orderby_order"]);
		}else{
		$this->db->order_by("city_name", "asc");
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
 		
 		$citys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("city_list");   ///segment 4 pag
 
		$this->data["citys"]=$this->modules->city_list($citys);
		
		if($action=="list"){
		echo $this->data["citys"];
		exit;
		}
		
		$this->data["countries"]=$this->order_model->get_Countries()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('site_settings/city',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	$this->quick->Header("");
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
	    
		$this->form_validation->set_rules('city_name', $this->data["L"]["name"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('city_code', $this->data["L"]["code"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('country_code', $this->data["L"]["country"]." ".$this->data["L"]["code"], 'trim|required|xss_clean');	
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data_insert=$this->data["POST"];
	 		
	 		$this->db->insert("city_list",$data_insert);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." id city added  ");
	 		
	 	redirect("admin/city");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
	$this->data["countries"]=$this->order_model->get_Countries()->result_array();
	
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('site_settings/city_form',$this->data);
	}
    
    function edit($ids){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   		
   		$this->quick->Header("");
   	
	$this->data["POST"]=$this->input->post();
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('city_name', $this->data["L"]["name"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('city_code', $this->data["L"]["code"], 'trim|required|xss_clean');	
		$this->form_validation->set_rules('country_code', $this->data["L"]["country"]." ".$this->data["L"]["code"], 'trim|required|xss_clean');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data=$this->data["POST"];
	 		$this->db->where("city_code",$ids)->update("city_list",$data);
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
		
		$this->data["countries"]=$this->order_model->get_Countries()->result_array();
		
		$this->data["POST"]=$this->db->where("city_code",$ids)->get("city_list")->row_array();
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('site_settings/city_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("city_code",$this->input->post("city_id"))->delete("city_list");
			$this->quick_model->logs(implode(',',$this->input->post("city_id"))." idli citys deleted ");
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
		
			if($key=="name"){
				$message.= "<p> ".$this->data["L"]["keywords"]." : " .$value."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_city',"");
	redirect('admin/city');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$city_session=$this->sessiondd->userdata('city') ;
			
				$city_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('city',$city_session);
			
				
				if(	isset($city_session["orderby_order"]) ){
				
					if($city_session["orderby_order"]=="asc" ){
					
						$city_session["orderby_order"]="desc";
					}else{
					
						$city_session["orderby_order"]="asc";
					}
					
				}else{
				
					$city_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('city',$city_session);
			}
	
	redirect('admin/city');
	}
	

	
}