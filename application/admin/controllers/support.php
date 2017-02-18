<?php
class support extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"support_id" => $this->data["L"]["id"],
				"subject" => $this->data["L"]["subject"],
				"finished" => $this->data["L"]["status"],
				"message" => $this->data["L"]["message"], 
				"category_id" => $this->data["L"]["category"],
				"user_id" => $this->data["L"]["customer"],
				"date_added" => $this->data["L"]["date"]
				
			);
    
    }
    
    
    function get_user_id($firstname){
    	
    	return 	$this->user=$this->db->like("firstname",$firstname)->get("customer");
    }
    
    function where_work(){
    
			
			///get1 ///
			
			if($this->input->get("user_id")){
			
			$this->form_post_support["user_id"]=$this->input->get("user_id");
			$this->sessiondd->set_userdata('form_post_support',$this->form_post_support);
			}else{
			
			if(isset($this->form_post_support["user_id"])){
			unset($this->form_post_support["user_id"]);
			}
			$this->sessiondd->set_userdata('form_post_support',$this->form_post_support);
			}
			
			///get1 *///


		
		if(is_array($this->form_post_support)){
		
			$this->form_post_support_where=array_diff_key($this->form_post_support,$pattern=array("customer_name"=>"","expire_date"=>""));
			$this->db->where($this->form_post_support_where);
			
			if(isset($this->form_post_support["customer_name"])){
				
			
				$this->get_user_id($this->form_post_support["customer_name"]);
				if($this->user->num_rows()>0){
					
					$this->db->where_in("user_id",	array_column($this->user->result_array(), "customer_id")	);
				}
				
			}
			
			if(isset($this->form_post_support["expire_date"])){
				$this->db->where("expire_date <=",strtotime($this->input->post("expire_date")));
			}
			
			if(isset($this->form_post_support["date_added"])){
			
				$date_filter=explode(" - ",$this->form_post_support["date_added"]);
				$this->db->where("date_added between ".strtotime($date_filter[0])." and ".strtotime($date_filter[1]));
			}
			
		}else{
		$this->form_post_support=array();
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
		
			$this->form_post_support=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_support',$this->form_post_support);
		}else{
		
			$this->form_post_support=$this->sessiondd->userdata('form_post_support') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_support;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->where("top_id",0)->get('support')->num_rows();//$this->user->getTotalUsers(); 
	
		$per_page=15;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/support/index';
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
		
		$support_session=$this->sessiondd->userdata('support');
		
		//$this->dbg2($support_session);
		if (	isset($support_session["orderby"])		){
		
		$this->db->order_by($support_session["orderby"], $support_session["orderby_order"]);
		}else{
		$this->db->order_by("support_id", "desc");
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
 		
 		
 		$supports=$this->db->where("top_id",0)->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("support");   ///segment 4 page
		//$this->dbg2($supports->result_array());
		$this->data["supports"]=$this->modules->support_list($this->smarty,$supports);
		
		if($action=="list"){
		echo $this->data["supports"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('support/support',$this->data);
	 
	}
	function change_status(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
		 
		//dbg($this->input->post());
		 
		if($this->input->post("status")=="true"){
	
			$status=0;
		}else {
	
			$status=1;
		}
		 
		if($this->input->post("support_id")>0){
	
			$this->db->where("support_id",$this->input->post("support_id"))->update("support",array("finished"=>$status ));
	
		}
		 
		echo 1; exit;
		 
	}
	
	function ticket($top_id) {
		$this->permission->check_permission("view");
    	
		
       $TICKETS=$this->db->order_by("support_id","desc")
       ->where(array("top_id"=>$top_id))
       ->or_where(array("support_id"=>$top_id))
       ->get("support")->result_array();
       
        foreach($TICKETS as $TICKET){
        
       	$TICKET["date_added"]=date("d/m/Y H:i",$TICKET["date_added"]);
       	
       	if($TICKET["user_type"]==2){
       		$TICKET["customer"]=$this->db->where("customer_id",$TICKET["user_id"])->get("admin")->row_array();
       	}else{
       		$TICKET["customer"]=$this->db->where("customer_id",$TICKET["user_id"])->get("customer")->row_array();
       	}
        $TICKETS2[]=$TICKET;
        }
       
       $this->data["TICKETS"]=$TICKETS2;
       $this->data["main_ticket"]=end($TICKETS2);

		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");	
		
    $this->smarty->view('support/ticket',$this->data);	
    }
    
    function submitaticket() {
    	
    	if(  $this->input->post() && $this->quick->logged_in()  ){
     	
     	$data_support=array(
	        	"subject" 	=> $this->input->post("title"),	
	        	"category_id"	=> $this->input->post("category"),
	        	"message" 	=> $this->input->post("message"),
	        	"user_id" 	=> utf8_encode($this->quick->get_admin2()->customer_id),
	        	"customer_id" 	=> $this->input->post("customer_id"),
	        	"user_type" 	=> 2,
	        	"top_id" 	=> $this->input->post("top_id"),
	        	"date_added" 	=> mktime()
	        	
	        	
        	);
     	
		if($_FILES["attachment"]["error"]!=4){
			$config['upload_path'] ='./uploads/support/';
			$config['file_name'] = mktime()+rand(1,5);
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '100';
			$config['max_width']  = '1024';
			$config['max_height']  = '768';
		

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload("attachment"))
			{
				$error = $this->upload->display_errors();
				//$this->quick->dbg2($error);
				//$this->load->view('upload_form', $error);
			}
			else
			{
				$file_data =  $this->upload->data();
				//$this->quick->dbg2($this->upload->data());
				$data_support["attachment"]="uploads/support/".$file_data['file_name'];

			}
		
		}
		
		$this->db->insert("support",$data_support);
	
		if($this->input->post("top_id")!=0){	
	
     	redirect('admin/support/ticket/'.$this->input->post("top_id"));
     	}else{
     	
     	redirect('admin/support/mysupport');
     	}
     	}
     	
       	$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
       $this->smarty->view('support/submitaticket');	
	}

    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("support_id",$this->input->post("support_id"))->delete("support");
			
			$this->quick_model->logs(implode(',',$this->input->post("support_id"))." idli supportlar silindi ");
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
		
			if($key=="customer_id"){
				$customer=$this->db->where("customer_id",$value)->get("customer")->row_array();
				$message.= "<p>".$this->shape[$key].":".$customer["firstname"]." ".$customer["lastname"]." </p>";
			}elseif($key=="customer_name"){
				
				$message.= "<p>customer name: ". $value."  </p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
			
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_support',"");
	redirect('admin/support');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$support_session=$this->sessiondd->userdata('support') ;
			
				$support_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('support',$support_session);
			
				
				if(	isset($support_session["orderby_order"]) ){
				
					if($support_session["orderby_order"]=="asc" ){
					
						$support_session["orderby_order"]="desc";
					}else{
					
						$support_session["orderby_order"]="asc";
					}
					
				}else{
				
					$support_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('support',$support_session);
			}
	
	redirect('admin/support');
	}
 
	
	
	
		
}