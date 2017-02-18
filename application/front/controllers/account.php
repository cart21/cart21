<?php


class Account extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       $this->load->model("product_model");
      $this->modules->account_left();
    }
 
    function index() {
		$data=array();

		$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
		
		$customer=$this->sessiondd->userdata('customer');
		$this->smarty->assign("username",$customer["firstname"] );
        
        if($this->quick->logged_in()) {
       
			$this->smarty->view('account/account',$this->data);	
		}else{

			redirect('account/login');
        }
        
    }
    
    function login($action="") {
    	
		if($this->quick->logged_in()){ redirect(base_url()); }
		
		$this->data["action"]=$action;
		
		$this->quick->Header("");
		
		$data=array();
    	
		$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        
        /// action register ///
        if($this->input->post("action")=="register"){
        	
        	$this->data["POST_r"]=$this->input->post();
        	$this->form_validation->set_rules("email", $this->language_model->language_c_key("email"), "trim|required|xss_clean|valid_email|callback_check_email");
        	$this->form_validation->set_rules("firstname", $this->language_model->language_c_key("firstname"), "trim|required|xss_clean");
        	$this->form_validation->set_rules("lastname", $this->language_model->language_c_key("lastname"), "trim|required|xss_clean");
        	$this->form_validation->set_rules("password", $this->language_model->language_c_key("password"), "trim|required|xss_clean|matches[re-password]");
        	$this->form_validation->set_rules("agreement", $this->language_model->language_c_key("registeragrement"), "trim|required|xss_clean");
        	
        	if( $this->input->post() && !$this->quick->logged_in()  ){
        		 
        		if( $this->form_validation->run() ){
        			 
        			$this->load->model('Account_Model');
        			$salt = substr(sha1(uniqid(rand(), true)), 0,6) ;
        			$data_customer=array(
        					"firstname" 	=> $this->input->post("firstname"),
        					"lastname"	=> $this->input->post("lastname"),
        					"email" 	=> $this->input->post("email"),
        					"telephone" => $this->input->post("telephone"),
        					"fax" 		=> $this->input->post("fax"),
        					"salt" 		=> $salt,
        					"date_added"=> mktime(),
        					"password" 	=> sha1($this->input->post("password")),
        					"ip" 		=> $_SERVER["REMOTE_ADDR"]
        			);
        			$customer_id=$this->Account_Model->add_customer($data_customer);
        			
        			$sesion=array(
        					"customer_id" =>$customer_id,
        					"firstname" =>$this->input->post("firstname"),
        					"lastname" =>$this->input->post("lastname"),
        					"email" =>$this->input->post("email")
        			);
        				
        			$this->sessiondd->set_userdata('customer',$sesion);
        			$this->translate_cart();
        			
        			 
        			//$text_message=" New Registration!";
        				
        			//SendMailWithSMTP($this->data["settings"]["email"],$this->data["settings"]["site_title"].' New registration',$text_message,$From="");
        			
        			
        			//mail
        			$this->load->helper('phpmailer');
        			$this->load->library("email_template");
        			
        			$data=array("template_id"=>1,"keys"=>array("firstname" 	=> $this->input->post("firstname"),"lastname"	=> $this->input->post("lastname")));
        			
        			$this->email_template->set_template($data);
        			
        			$subject=$this->email_template->Template["subject"];
        			$this->email_template->Template["subject"]=$subject;
        			
        			$this->email_template->SendMailWithGmailSMTP($this->input->post("email"));
        			
        			$this->email_template->Template["subject"]=$subject." Yeni Üye Kayıt oldu";
        			$this->email_template->SendMailWithGmailSMTP($this->data["settings"]["email"]);
        			//mail
        			 
        			redirect($this->quick_model->get_link("/account/profileSettings"),'refresh');
        			 
        		}else {
        			$verrors=array_filter(explode('.',validation_errors()));
        			foreach($verrors as $verror){
        				$this->quick->errors[] = strip_tags($verror).".";
        			}
        		}
        	}
        	
        }else{
        	/// action login
        $this->data["POST_l"]=$this->input->post();
    	$this->form_validation->set_rules("email", $this->language_model->language_c_key("email"), "trim|required|xss_clean");
    	$this->form_validation->set_rules("password", $this->language_model->language_c_key("password"), "trim|required|xss_clean");
      	 
      	if(  $this->input->post() && !$this->quick->logged_in()  ){
      	
			if ( $this->form_validation->run()== FALSE ){
			
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
		
			}else{
				
				/// cart21 check login ///
				$email=$this->db->escape($this->input->post("email"));
				$password=sha1($this->input->post("password"));
				$customer = $this->db->query("SELECT * FROM customer WHERE  email=$email AND password='$password' LIMIT 1");
				
				if($customer->num_rows()>0){
			
					$pattern=array(
					"customer_id" =>"",
					"firstname" =>"",
					"lastname" =>"",
					"email" =>""
					);
					$sesion=array_intersect_key($customer->row_array(),$pattern);
			
				$this->sessiondd->set_userdata('customer',$sesion);
				
				$this->translate_cart();
				redirect($this->quick_model->get_link("/account/profileSettings"));
				}else{
				$this->quick->errors[] =$this->language_model->language_c_key("logintext1");//"Please Try again inccorect information!";
				}
			}
		}
		
		
          }// action else
		
		 /*// meta ///
        $uri=substr($_SERVER["REQUEST_URI"],1);
		$db_routes=$this->db->limit(1)->get_where("meta",array("class_routes"=>"/account/login"));

		if($db_routes->num_rows()>0){
		
			$this->data["meta"]=$db_routes->row_array(); 
			
				// language load
				$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["meta_id"]))->result_array(),"text_val","key_val");
			
		}else{
		
        }
        
        /// meta //*/
	
		
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        $this->smarty->view("account/login",$this->data);
 
    }
    
    function translate_cart(){
    	
    	$sess=$this->sessiondd->all_userdata();
    	$this->product_model->cart_clear();
    	if(isset($sess["cart"]["products"])){
    	foreach ($sess["cart"]["products"] as $p ){
    		
    		unset($p["cart_id"]);
    		$p["customer_id"]=$sess["customer"]["customer_id"];
    		
    		$this->db->insert("cart",$p);
    	}
    	unset($sess["cart"]["products"]);
    	
    	$this->sessiondd->set_userdata("cart",$sess["cart"]);
    	}
    }
    
    function logout() {
    	
    
	$this->sessiondd->unset_userdata("customer");
   redirect('/','refresh');
   }
    
    
    function address() {
    	
    	$this->quick->onlyLoginUser();
   		
   		$this->load->model('Account_Model');
    	
        if( $this->input->post() ){
        
        	$data_address=array(
        			
	        	"firstname" 	=> $this->input->post("firstname"),	
	        	"lastname"	=> $this->input->post("lastname"),
	        	"company" 	=> $this->input->post("company"),
	        	"company_id" 	=> $this->input->post("company_id"),
	        	"tax_id" 	=> $this->input->post("tax_id"),
	        	"address_1" 	=> $this->input->post("address_1"),
	        	"address_2" 	=> $this->input->post("address_2"),
	        	"city_code" 	=> $this->input->post("city_code"),
	        	"city" 		=> $this->input->post("city"),
	        	"postcode" 	=> $this->input->post("postcode"),
	        	"country_code" 	=> $this->input->post("country_code"),
	        	"zone_id" 	=> $this->input->post("zone_id")
        	);	
        	
        	$this->Account_Model->add_address($data_address);
        }
        
		$this->data["Address"]=$this->Account_Model->get_Address()->result_array();
		$this->data["countries"]=$this->Account_Model->get_Countries()->result_array();
		
		$this->data["myadress_link"]=$this->quick_model->get_link("/account/address");
	
		$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
	
	$this->smarty->view('account/address_form.tpl',$this->data);
       
    }
    
    function addressEdit($address_id) {
    
    	$this->quick->onlyLoginUser();
   
    	$this->load->model('Account_Model');
        
        if( $this->input->post() ){
       
        	$data_address=array(
        			
	        	"firstname" 	=> $this->input->post("firstname"),	
	        	"lastname"	=> $this->input->post("lastname"),
	        	"company" 	=> $this->input->post("company"),
	        	"company_id" 	=> $this->input->post("company_id"),
	        	"tax_id" 	=> $this->input->post("tax_id"),
	        	"address_1" 	=> $this->input->post("address_1"),
	        	"address_2" 	=> $this->input->post("address_2"),
	        	"city_code" 	=> $this->input->post("city_code"),
	        	"city" 		=> $this->input->post("city"),
	        	"postcode" 	=> $this->input->post("postcode"),
	        	"country_code" 	=> $this->input->post("country_code"),
	        	"zone_id" 	=> $this->input->post("zone_id")
        	);	
        	
        	$this->Account_Model->edit_address($data_address,$address_id);
        	
        	$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
        }
        
        
        $this->data["page"]="edit";
        $Address=$this->Account_Model->get_Address();
        $this->data["Address"]=$Address->result_array();
        
		$Address=$this->Account_Model->get_Addres($address_id);
	
		$this->smarty->assign("Addressone",$Address->row_array());
		
		
		$this->smarty->assign("Cities",$this->Account_Model->get_Cities($Address->row()->country_code)->result_array());
		$this->smarty->assign($Address->row()->city_code,"selected");
			/// city country selected ///
		
			$this->smarty->assign("action","update");
			$this->smarty->assign("address_id",$address_id);
		
			$meta = $this->quick_model->meta(array("type"=>1,"class_routes"=>"/account/address"));
			 
			if($meta->num_rows >0 ){
				$this->data["meta"]=$meta->row_array();
				// language load
				$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["meta_id"]))->result_array(),"text_val","key_val");
				 
			}else{
				 
			}
			
			$this->data["myadress_link"]=$this->quick_model->get_link("/account/address");
			
			$this->data["countries"]=$this->Account_Model->get_Countries()->result_array();
		
			$this->quick->Header("");
			$this->quick->Top_menu("");
			$this->quick->Footer("");
    $this->smarty->view('account/address_edit_form',$this->data);
       
    }
    
    function forgotten() {
    
    	$this->quick->Header("");
    	 
    	$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    	
    	$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean|valid_email|callback_email_exist');
    	
    		if(  $this->input->post()  && $this->form_validation->run() ){
    		
    		
			$newpassword=rand(100000,99999999);  
					
			$this->db->where("email",$this->input->post("email"));  
			$this->db->update("customer",array("password"=>sha1($newpassword)));	

			$this->load->library('CIphpmailer');
			 
			$text_message="";
			$text_message.='<p> '.$this->data["L"]["new"]." ".$this->data["L"]["password"]. ':'.$newpassword.'</p>';
			
			$subject=$this->data["L"]["new"]." ".$this->data["L"]["password"];
				
			$r=$this->ciphpmailer->SendMailWithSMTP($this->input->post("email"),$subject,$text_message,$From="");
			
			if($r){
				$this->quick->success[] =$this->language_model->language_c_key("newslettertext1");
				$this->data["POST"]="";
			}else{
				$this->quick->errors[] =$this->ciphpmailer->phpmailer->ErrorInfo;
			}
			
    		}else{
    		echo "";
    		
    		}
    	
    	
       
        $this->quick->Top_menu("");
        $this->quick->Footer("");

 	$this->smarty->view('account/forgotten',$this->data);
 
    }
    
    function email_exist($email){
    	
    	
    	$customer=$this->db->where("email",$email)->get("customer");
    	//$this->quick->dbg2($customer->row());
    	
    	if($customer->num_rows>0){
    	return true;
    	}else{
    	 $this->smarty->assign("error",$this->input->post("email")."this email is not registered ");
    	return false;
    	}
    
    }
    
    function changePassword() {
    
    	$this->quick->onlyLoginUser();
    	
    	$this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
    	
	
		$this->form_validation->set_rules('current_password', 'Password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('new_password', 'Confirm password', 'trim|required|xss_clean|matches[confirm_password]');
		$this->form_validation->set_rules('confirm_password', 'new_password Confirmation', 'trim|required|xss_clean');
	
      	if(  $this->input->post() && $this->quick->logged_in() && $this->form_validation->run() ){
      		
	      	$customer=$this->sessiondd->userdata('customer');
	        
	        $password=sha1($this->input->post("current_password"));
	        $new_password=sha1($this->input->post("new_password"));
	        
			$query_customer= $this->db->query("SELECT * FROM customer  WHERE  customer_id=". $customer["customer_id"]." AND password='$password' LIMIT 1");
	        
	        if($query_customer->num_rows()==1){
		        $this->db->query("UPDATE customer SET  password='$new_password' WHERE  customer_id=". $customer["customer_id"]." AND password='$password'");
	        }
	        $this->quick->success[] ="Successful";
      	
      	}else {
        			$verrors=array_filter(explode('.',validation_errors()));
        			foreach($verrors as $verror){
        				$this->quick->errors[] = strip_tags($verror).".";
        			}
        		}
      	
      	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");

 	$this->smarty->view('account/changePassword',$this->data);
 
    }
    
    function deleteAddress($address_id) {
    
       $this->quick->onlyLoginUser();
       
    	if($address_id){
    	
    		$this->db->where('address_id',$address_id )->delete('address');
		}
		echo "1" ;exit;
    
    }
    
    function profileSettings(){
    
		$this->quick->onlyLoginUser();
		$this->load->library('form_validation');
	
		/// cart21 profileSettings rules ///
		$this->form_validation->set_rules("email", $this->language_model->language_c_key("email"), "trim|required|xss_clean|valid_email|callback_check_email_edit");
		$this->form_validation->set_rules("firstname", $this->language_model->language_c_key("firstname"), "trim|required|xss_clean");
		$this->form_validation->set_rules("lastname", $this->language_model->language_c_key("lastname"), "trim|required|xss_clean");
		
		if( $this->input->post() && $this->form_validation->run() ){
			
			$this->data["profile"]= $this->input->post();
		 
			$this->db->where('customer_id', $this->quick->logged_customer()->customer_id)->update('customer', $this->data["profile"]);
			
			$this->quick->success[] = $this->language_model->language_c_key("successfuledit");
		
			}else  {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
			
			$this->data["user"]=$this->quick->logged_customer();
	
			$this->quick->Header("");
			$this->quick->Top_menu("");
			$this->quick->Footer("");

		
	$this->smarty->view('account/profileSettings',$this->data);
	}
    
    function escapeAll($data){
	    	foreach($data as $key => $value){
	    	$this->data[$key]=$this->db->escape($value);
	    	
	    	}
    	return $data;
    	}
    	
    function check_capcha($field) {
    
    $response=$this->recaptchalib->check_capcha($field);
    
    if(!$response){
    $this->form_validation->set_message('check_capcha', 'The %s  wrong "');
    }
    return  $response;
    // 
    }
    
    function my_orders(){
    	
    	$this->quick->onlyLoginUser();
    	
    	$orders=$this->product_model->get_my_orders();
    	$customer=$this->quick->logged_user();
    	foreach( $orders->result_array() as $order){
    			
    		$order["customer_id"]=$customer->firstname." ".$customer->lastname;
    		$order["date_added"]=date("d-m-Y H:i",$order["date_added"]);
	    	$order["order_status"]=$this->product_model->order_status($order["order_status"])->row();
	    
    		$order["bank_id"]=$this->product_model->bank($order["bank_id"])->row();
	    
    		$order["shipping_status"]=$this->product_model->order_shipping_status($order["shipping_status"]);
    		
    			
    		$this->data['orders'][]=$order;
    	}
    	
    	
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    	$this->smarty->view('account/myorders',$this->data);
    }
    
    function order_view($ids){
    
    	$this->data["page"]="view";
    		
    	$this->data["order"]=$this->product_model->order($ids);
    	
    	if($this->data["order"]->num_rows()<=0 ){
    
    		redirect(base_url());
    		exit;
    	}
    		
    	$this->data["order"]=$this->data["order"]->row_array();
    
    	
    		
    	////
    	$this->data["order"]["date_added"]=date("d-m-Y H:i",$this->data["order"]["date_added"]);
    	
    	$this->data["order"]["address"]=unserialize($this->data["order"]["address"]);
    		
    	$this->data["payment_type"]=$this->product_model->order_status($this->data["order"]["order_status"])->row();
    
    	$this->data["shipping_status"]=$this->product_model->order_shipping_status($this->data["order"]["shipping_status"])->row();
    
    	$this->data["bank"]=$this->product_model->bank($this->data["order"]["bank_id"])->row();
    
    	$this->data["shipping_company"]=$this->product_model->shipping_company($this->data["order"]["shipping_company_id"])->row();

    	
    	if($this->data["order"]["non_member"]){
    		$this->data["order"]["non_member"]=unserialize($this->data["order"]["non_member"]);
    	}
    	 
    	////
    
    	/// Products
    	$this->data["order_products"]=$this->product_model->order_products($ids)->result_array();
    	$this->data["total_with_tax"]=0;
    	//dbg($this->data["order_products"]);
    	foreach( $this->data["order_products"] as $k=>$p ){
    	
    		if(! is_null($p["product_features"]) ){
    			 
    			 $this->data["order_products"][$k]["product_feature"]=unserialize( $p["product_features"]);
    			 
    			 $this->data["order_products"][$k]["product_feature"]=$this->product_model->product_feature( $this->data["order_products"][$k]["product_feature"]["features"])->result_array();
    		}else{
    			 $this->data["order_products"][$k]["product_feature"]="";
    		}
    		
    
    		$this->data["total_with_tax"] +=$p["basket_price"]*$p["number"];
    	}
    	
    	$meta = $this->quick_model->meta(array("type"=>1,"class_routes"=>"/account/my_orders"));
    	
    	if($meta->num_rows >0 ){
    		$this->data["meta"]=$meta->row_array();
    		// language load
    		$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["meta_id"]))->result_array(),"text_val","key_val");
    		 
    	}else{
    		 
    	}
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('account/order_view',$this->data);
    
    
    }
    
    
    function my_favorites(){
    	 
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	$this->smarty->view('account/myfavorites',$this->data);
    }
    
    function check_email($email){
    
    	$meta=$this->db->where("email",$email)->get("customer");
    
    	if($meta->num_rows>0){
    		 
    		$this->form_validation->set_message('check_slug', 'The email '.$meta->row()->email.' %s is already in use ');
    		return false;
    	}else{
    		 
    		return true;
    	}
    
    }
    
    function check_email_edit($email){
    	
    	$this->data["user"]=$this->quick->logged_customer();
    	$sql="select * from customer where customer_id <>".$this->data["user"]->customer_id." and email='$email' ";
    	$meta=$this->db->query($sql);
    
    	if($meta->num_rows>0){
    		 
    		$this->form_validation->set_message('check_email_edit', 'The email '.$meta->row()->email.' %s is already in use ');
    		return false;
    	}else{
    		 
    		return true;
    	}
    
    }
    
   
     ///account_end
		
}