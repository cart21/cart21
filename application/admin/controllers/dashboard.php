<?php
class Dashboard extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();

        $this->load->model('task_model');
        $this->load->model('today_model');
        $this->load->model('order_model');
    }
 
    
    function index() {

		$this->quick->onlyLoginUser();

		$this->quick->Header("");
		
	 	$this->data["dd"]="dd";
	 	$category_stats=$this->db->query("SELECT pc.`title`, count(*) as total FROM `product` as p
			 	
		left join product_category as pc
		on p.`category_id`=pc.product_category_id
			 	
		group by `category_id` ");
	 	 
	 	$this->data["pie"]=json_encode($category_stats->result_array());
	 	
	 	/*// Admin logs ///
	 	$a_logs=$this->db->order_by("logs_id","desc")->limit(25)->get("admin_logs");
	 	if($a_logs->num_rows){
		 	foreach( $a_logs->result_array() as $log){
		 	
		 		$log["date_added"]=date('d/m/Y  H:i',$log["date_added"]);
		 			
		 		if($log["user_type"]){
		 			$log["user_id"]="customer";
		 		}else{
		 			
		 			$admin=$this->db->where("customer_id",$log["user_id"])->get("admin");
		 			if($admin->num_rows){
		 			$log["user_id"]=$admin->row()->firstname." - ".$admin->row()->lastname ;
		 			}
		 			
		 		}
		 		$this->data['logs'][]=$log;
		 	}
	 	}
	 	/// Admin logs //*/
	 	
	 	
	 	/*// customer logs ///
	 	$c_logs=$this->db->where("user_type",1)->order_by("logs_id","desc")->limit(25)->get("admin_logs");
	 	if($c_logs->num_rows){
	 	foreach( $c_logs->result_array() as $log){
	 		 
	 		$log["date_added"]=date('d/m/Y  H:i',$log["date_added"]);
	 			
	 		
	 			$c=$this->db->where("customer_id",$log["user_id"])->get("customer");
	 			if($c->num_rows){
	 			$log["user_id"]=$c->row()->firstname." - ".$c->row()->lastname ;
	 			}else{
	 				$log["user_id"]="non member";
	 			}
	 		 
	 		$this->data['customer_logs'][]=$log;
	 	}
	 	}
	 	/// customer logs //*/
	
	 	
	 	/// orders ///
	
	 	$orders=$this->db->order_by("order_id","desc")->limit(12)->get('order');
	 	foreach( $orders->result_array() as $order){
	 			
	 		$customer=$this->User->customer2($order["customer_id"]);
	 		if($customer->num_rows){
	 			$order["customer_id"]=$customer->row()->firstname." ".$customer->row()->lastname;
	 		}else{
	 			$order["customer_id"]=$this->data["L"]["nonmember"];
	 		}
	 	
	 		$order["date_added"]=date("d-m-Y H:i",$order["date_added"]);
	 	
	 		$order["order_status"]=$this->order_model->order_status($order["order_status"])->row();
	 		$order["bank_id"]=$this->order_model->bank($order["bank_id"])->row();
	 			
	 		$this->data['orders'][]=$order;
	 	}
	 	$this->data["dash_settings"]=$this->db->where("settings_options_id",$this->data["settings"]["settings_options_id"])->get("settings_options")->row_array();
	 	
	 	///check smtp status ///
	 	if($this->data["settings"]["use_smtp"]){
	 		$this->load->library("ci_phpmailer");
	 		$this->data["smtp_status"]=$this->ci_phpmailer->set_phpmailer();
	 	}else{
	 		$this->data["smtp_status"]=false;
	 	}
	 	///check smtp status ///
	 	
		$this->quick->Top_menu("");
		$this->quick->Footer("");
		
    $this->smarty->view('Dashboard',$this->data);
 
    }
    
    function change_language($id){
    
    	$language=$this->language_model->language(array("language_id"=>$id));
    
    	if($language->num_rows){
    		$_SESSION["cart21_a_language"]["language_id"]=$language->row()->language_id;
    		$_SESSION["cart21_a_language"]["name"]=$language->row()->name;
    		$_SESSION["cart21_a_language"]["short_name"]=$language->row()->short_name;
    		$_SESSION["cart21_a_language"]["image"]=$language->row()->image;
    	}
    		
    	echo '1'; exit;
    }
    

    
    function smtp_status(){
    	
    }
    
    
    function dd(){

    
    	
    	
    }
     
		
}