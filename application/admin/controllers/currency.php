<?php
class currency extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('quick_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"currency_id" => $this->data["L"]["id"],
    			"name" => $this->data["L"]["currency"],
    			"short_name" => $this->data["L"]["sign"],
    			"sign" => $this->data["L"]["sign"]

			);
    
    }
    
    function where_work(){
    
		if(is_array($this->form_post_currency)){
		
			$this->form_post_currency_where=array_diff_key($this->form_post_currency,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_currency_where);
			
			
			if(isset($this->form_post_currency["link"])){
				$this->db->like("link",$this->form_post_currency["link"]);
			}
			
			if(isset($this->form_post_currency["class_routes"])){
				$this->db->like("class_routes",$this->form_post_currency["class_routes"]);
			}
			
		}else{
		$this->form_post_currency=array();
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
		
			$this->form_post_currency=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_currency',$this->form_post_currency);
		}else{
		
			$this->form_post_currency=$this->sessiondd->userdata('form_post_currency') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_currency;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('currency')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/currency/index';
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
		
		$currency_session=$this->sessiondd->userdata('currency');
		
		if (	isset($currency_session["orderby"])		){
		
		$this->db->order_by($currency_session["orderby"], $currency_session["orderby_order"]);
		}else{
		$this->db->order_by("currency_id", "desc");
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
 		
 		$currencys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("currency");   ///segment 4 page
		$this->data["currencys"]=$this->modules->currency_list($this->smarty,$currencys);
		
		if($action=="list"){
		echo $this->data["currencys"];
		exit;
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('currency/currency',$this->data);
	 
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
	 		$data["sign"]=$this->input->post("sign");
	 		
	 		$this->db->insert("currency",$data);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." id currency added  ");
	 		
	 	redirect("admin/currency");
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
	$this->smarty->view('currency/currency_form',$this->data);
	
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
	    
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');	
		$this->form_validation->set_rules('short_name', 'Code', 'trim|required|xss_clean');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		$data["name"]=$this->input->post("name");
	 		$data["short_name"]=$this->input->post("short_name");
			$data["sign"]=$this->input->post("sign");
	 		
	 		$this->db->where("currency_id",$ids)->update("currency",$data);
	 		
	 		$this->quick->success[]="Succesfully edited";
	 		
	 
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		
		$this->data["POST"]=$this->db->where("currency_id",$ids)->get("currency")->row_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('currency/currency_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("currency_id",$this->input->post("currency_id"))->delete("currency");
			$this->quick_model->logs(implode(',',$this->input->post("currency_id"))." idli currencylar silindi ");
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
		
			if($key=="currency_group"){
				$group=$this->db->where_in("currency_group_id",$value)->get("currency_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_currency',"");
	redirect('admin/currency');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$currency_session=$this->sessiondd->userdata('currency') ;
			
				$currency_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('currency',$currency_session);
			
				
				if(	isset($currency_session["orderby_order"]) ){
				
					if($currency_session["orderby_order"]=="asc" ){
					
						$currency_session["orderby_order"]="desc";
					}else{
					
						$currency_session["orderby_order"]="asc";
					}
					
				}else{
				
					$currency_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('currency',$currency_session);
			}
	
	redirect('admin/currency');
	}
	

	
}