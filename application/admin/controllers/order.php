<?php
class order extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('order_model');
        $this->load->model('product_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"order_id" => "ID",
    			"customer_id" => "müşteri",
    			"total_all" => "Total all",
    			"total_pure" => "Total Pure",
    			"order_status" => "Payment",
    			"bank_id" => "bank",
    			"shipping_status" => "Shipping Status",
    			"order_note" => "note",
    			"date_added" => "Date"
    			
			);
    
    }
    
    function where_work(){
    
			
		
		if(is_array($this->form_post_order)){

			$this->form_post_order_where=array_diff_key($this->form_post_order,$pattern=array("date_added"=>"date_added","customer_name"=>""));
			$this->db->where($this->form_post_order_where);
			
			
			if(isset($this->form_post_order["customer_name"])){
					
				$customer=$this->db->query("select customer_id from customer where firstname like '%".$this->form_post_order["customer_name"]."%' or lastname like '%".$this->form_post_order["customer_name"]."%' ");
			}
			
			if(isset($this->form_post_order["date_added"])){
				
				$date_filter=explode(" - ",$this->form_post_order["date_added"]);
				$this->db->where("date_added between ".strtotime($date_filter[0])." and ".strtotime($date_filter[1]));
			}
			
			
			
			
		}else{
		$this->form_post_order=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){

		$this->permission->check_permission("view");

		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');

		$this->data["order_status_all"]=$this->order_model->order_status_all();
		$this->data["order_shipping_status_all"]=$this->order_model->order_shipping_status_all();
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_order=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_order',$this->form_post_order);
		}else{
		
			$this->form_post_order=$this->sessiondd->userdata('form_post_order') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_order;

		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('order')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=40;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/order/index';
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
		
		$config['cur_tag_open'] = ' <li><a href="#" class="bg-navy text-white"> ';
		$config['cur_tag_close'] = '</a></li>';
		
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
		
		$order_session=$this->sessiondd->userdata('order');
		
		//$this->dbg2($order_session);
		if (	isset($order_session["orderby"])		){
		
		$this->db->order_by($order_session["orderby"], $order_session["orderby_order"]);
		}else{
		$this->db->order_by("order_id", "desc");
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
 		
 		$orders=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("order");   ///segment 4 page
		
		$this->data["orders"]=$this->modules->order_list($orders);
		
		if($action=="list"){
		echo $this->data["orders"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('order/order',$this->data);
	 
	}
	
	
	function change_status(){
		
		$order_id=$this->input->post("order_id");
		
		$this->data["order_shipping_status_all"]=$this->order_model->order_shipping_status_all()->result_array();
		
		
		$this->db->where("order_id",$order_id)->update("order",array("shipping_status"=>$this->input->post("shipping_status")));
		
		//
		$this->load->helper('phpmailer');
		$this->load->library("email_template");
		
		$ship_status=array_column($this->data["order_shipping_status_all"],"title","status_id");
		
		$order=$this->email_template->order($order_id);
		
		if($order["order"]["non_member"]){
			$email=$order["order"]["non_member"]["email"];
		}else{
			$email=$order["order"]["email"];
		}
		
		$data=array("template_id"=>2,"keys"=>$order);
		
		$this->email_template->set_template($data);
		
		$subject=$this->email_template->Template["subject"];
		$this->email_template->Template["subject"]=$subject ." ".$ship_status[$this->input->post("shipping_status")]." ";
		
		$this->email_template->SendMailWithGmailSMTP($email);
		
		$this->email_template->Template["subject"]=$subject." ".$ship_status[$this->input->post("shipping_status")] ." admin kargo durumunu değiştirdi";
		$this->email_template->SendMailWithGmailSMTP($this->data["settings"]["email"]);
		//
		
		echo 1;exit;
	}
 
    function edit($ids){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		
   		
   		
   		$this->data["order"]=$this->order_model->order($ids)->row_array();
   		$this->data["order"]["date_added"]=date("d-m-Y H:i",$this->data["order"]["date_added"]);
   		
   		$this->data["order_products"]=$this->order_model->order_products($ids)->result_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('order/order_form',$this->data);
    
	
    }

    function view($ids){
    
    	$this->permission->check_permission("view");
    	$this->data["page"]="view";
    	
    	$this->data["order"]=$this->order_model->order($ids);
    	
    	if($this->data["order"]->num_rows()<=0 ){
    		
    		redirect(base_url());
    		exit;
    	} 
    	
    	$this->data["order"]=$this->data["order"]->row_array();
    	
    	
    	////
    	$this->data["order"]["date_added"]=date("d-m-Y H:i",$this->data["order"]["date_added"]);
    	$this->data["order"]["address"]=unserialize($this->data["order"]["address"]);
    	
    	$this->data["payment_type"]=$this->order_model->order_status($this->data["order"]["order_status"])->row();
    	 
    	$this->data["shipping_status"]=$this->order_model->order_shipping_status($this->data["order"]["shipping_status"])->row();

    	$this->data["bank"]=$this->order_model->bank($this->data["order"]["bank_id"])->row();

    	$this->data["shipping_company"]=$this->order_model->shipping_company($this->data["order"]["shipping_company_id"])->row();
    	
    	if($this->data["order"]["non_member"]){
    		$this->data["order"]["non_member"]=unserialize($this->data["order"]["non_member"]);
    	}
    	
    	////
    	 
    	/// Products
    	$this->data["order_products"]=$this->order_model->order_products($ids)->result_array();
    	$this->data["total_with_tax"]=0;
    	$this->data["total_number"]=0;
    	
    	foreach( $this->data["order_products"] as $k=>$p ){
    		
    		if(! is_null($p["product_features"]) ){
    		
    			$this->data["order_products"][$k]["product_feature"]=unserialize( $p["product_features"]);
    		
    			$this->data["order_products"][$k]["product_feature"]=$this->product_model->product_feature( $this->data["order_products"][$k]["product_feature"]["features"])->result_array();
    		}else{
    			$this->data["order_products"][$k]["product_feature"]="";
    		}
    		
    		
    		$this->data["total_with_tax"] +=$p["basket_price"]*$p["number"];
    		
    		$this->data["total_number"]+=$p["number"];
    	}
    	
    	
    	$this->data["currency_sign"]=$this->db->where("currency_id",$this->data["settings"]["currency_id"])->get("currency")->row()->sign;
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('order/order_view',$this->data);
    
    
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			
			$this->db->where_in("order_id",$this->input->post("order_id"))->delete("order");
			$this->db->where_in("order_id",$this->input->post("order_id"))->delete("order_product");
				
			
			$this->quick_model->logs(implode(',',$this->input->post("order_id"))." idli orderlar silindi ");
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
		
		if($key=="order_group"){
				$group=$this->db->where_in("order_group_id",$value)->get("order_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}if($key=="customer_name"){
				$message.= "<p> Müşteri ismi:".$value."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_order',"");
	redirect('admin/order');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$order_session=$this->sessiondd->userdata('order') ;
			
				$order_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('order',$order_session);
			
				
				if(	isset($order_session["orderby_order"]) ){
				
					if($order_session["orderby_order"]=="asc" ){
					
						$order_session["orderby_order"]="desc";
					}else{
					
						$order_session["orderby_order"]="asc";
					}
					
				}else{
				
					$order_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('order',$order_session);
			}
	
	redirect('admin/order');
	}
	
 
	
	
		
}