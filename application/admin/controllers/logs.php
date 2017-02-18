<?php
class logs extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('quick_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"logs_id" => $this->data["L"]["id"],
				"user_id" => $this->data["L"]["user"],
				"user_type" => $this->data["L"]["admin"],
				"message" => $this->data["L"]["message"],
				'date_added'=>$this->data["L"]["date"],
				'user_ip'=> $this->data["L"]["ip"],
				'user_agent'=>$this->data["L"]["agent"]
			);
    
    }
    
    function where_work(){
    
		
		if(is_array($this->form_post_logs)){

			$this->form_post_logs_where=array_diff_key($this->form_post_logs,$pattern=array("date_added"=>"date_added","expire_date"=>""));
			$this->db->where($this->form_post_logs_where);
			
			if(isset($this->form_post_logs["date_added"])){
			
				$date_filter=explode(" - ",$this->form_post_logs["date_added"]);
				$this->db->where("date_added between ".strtotime($date_filter[0])." and ".strtotime($date_filter[1]));
			}
				
		}else{
		$this->form_post_logs=array();
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
		
			$this->form_post_logs=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_logs',$this->form_post_logs);
		}else{
		
			$this->form_post_logs=$this->sessiondd->userdata('form_post_logs') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_logs;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('admin_logs')->num_rows();
		
		$per_page=30;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/logs/index';
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
		
		$logs_session=$this->sessiondd->userdata('logs');
		
		//$this->dbg2($logs_session);
		if (	isset($logs_session["orderby"])		){
		
		$this->db->order_by($logs_session["orderby"], $logs_session["orderby_order"]);
		}else{
		$this->db->order_by("logs_id", "desc");
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
 		
 		$logs=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("admin_logs");   ///segment 4 page
		$this->data["logs"]=$this->modules->logs_list($this->smarty,$logs);
		
		if($action=="list"){
		echo $this->data["logs"];
		exit;
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
	$this->smarty->view('logs/logs',$this->data);
	 
	}
	
	
    
	function get_filter_message(){
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if(in_array($key,array("expire_date","begin_date"))){
				
				$message.= "<p>:".$value."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_logs',"");
	redirect('admin/logs');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$logs_session=$this->sessiondd->userdata('logs') ;
			
				$logs_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('logs',$logs_session);
			
				
				if(	isset($logs_session["orderby_order"]) ){
				
					if($logs_session["orderby_order"]=="asc" ){
					
						$logs_session["orderby_order"]="desc";
					}else{
					
						$logs_session["orderby_order"]="asc";
					}
					
				}else{
				
					$logs_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('logs',$logs_session);
			}
	
	redirect('admin/logs');
	}
 
	
		
}