<?php
class cart extends CI_Controller {
 
    function __construct() {
        parent::__construct();

        $this->load->model("product_model");
        $this->load->model("account_model");
    }
 
    function index($dd="") {

   		$meta = $this->quick_model->meta(array("type"=>1,"class_routes"=>"cart"));
   		
   		if($meta->num_rows >0 ){ 
   			$this->data["meta"]=$meta->row_array();
	   		// language load
	   		$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["m_id"]))->result_array(),"text_val","key_val");
   		
   		}else{
   			
   		}		
   		
   		$this->data["shipping_company"]=$this->product_model->shipping_companies();
   		$this->data["payment_type"]=$this->product_model->order_status_all()->result_array();
   		
   		if($this->quick->logged_in()){
   		$this->data["Address"]=$this->account_model->get_Address();
   		}
   		
   		$this->data["countries"]=$this->account_model->get_Countries()->result_array();
   		
   		$cart_product=$this->product_model->cart_products();
		$this->data["cart_products"]=$cart_product;
		$this->data["cart_summary"]=$cart_product["cart_summary"];
		
			
   		$this->data["number_person"]=range(0,20); unset($this->data["number_person"][0]);
   		
   		
   		$this->data["POST"]=$this->input->post();
   		
   		/// check
   		$cart_control=true;
   		
   		if(count($this->data["cart_products"]["products"])<=0){
   				
   			//$this->quick->errors[]=$this->language_model->language_c_key("emptycart");
   			$cart_control=false;
   		}
   		
   		if($this->data["cart_products"]["check_stock"]== false and $cart_control==true){
   				
   			$this->quick->errors[]=$this->language_model->language_c_key("cartstock_error");
   			$cart_control=false;
   		}
   		 
   		/// check
   		
		if( $this->input->post() and $cart_control ){
			
		//set post session 	
		$this->sessiondd->set_userdata("payment",$this->data["POST"]);
			
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('order_status', 'Payment Option', 'trim|required|xss_clean');	
			if($this->data["cart_products"]["shipping"]){
				$this->form_validation->set_rules('shipping_company', 'Shipping', 'trim|required|xss_clean');
			}
			if(!$this->quick->logged_in()){

				$this->form_validation->set_rules('email', 'email', 'trim|required|xss_clean');
				$this->form_validation->set_rules('firstname1', 'Customer Name', 'trim|required|xss_clean');
				$this->form_validation->set_rules('lastname1', 'Customer Lastname', 'trim|required|xss_clean');
				

				$this->form_validation->set_rules('firstname', 'Firstname', 'trim|required|xss_clean');
				$this->form_validation->set_rules('lastname', 'Lastname', 'trim|required|xss_clean');
				$this->form_validation->set_rules('address_1', 'Address', 'trim|required|xss_clean');
				$this->form_validation->set_rules('country_code', 'Country', 'trim|required|xss_clean');
				$this->form_validation->set_rules('city_code', 'City', 'trim|required|xss_clean');
				
			}else{
				if($this->data["cart_products"]["shipping"]){
				$this->form_validation->set_rules('address_id', 'Address', 'trim|required|xss_clean');
				}

			}
			
			if($this->input->post("order_status")==1){
				$this->form_validation->set_rules('bank_id', 'Bank Transfer', 'trim|required|xss_clean');	
			}
			
			///before validation run ///
			
			if ($this->form_validation->run() and count($this->data["cart_products"]["products"])>0	) {
				
				if($this->quick->logged_in() ){
					
					$customer=$this->quick->logged_user();
					$order_data["customer_id"]=$customer->customer_id;
					$email=$customer->email;
					if($this->data["cart_products"]["shipping"]){
						$order_data["address"]=$this->account_model->get_Addres($this->data["POST"]["address_id"])->row_array();
						$order_data["address"]["city_code"]= $this->account_model->get_city($order_data["address"]["city_code"])->row()->city_name;
						$order_data["address"]["country_code"]=$this->account_model->get_country($order_data["address"]["country_code"])->row()->name;
						$order_data["address"]=serialize($order_data["address"]);
					}
				}else{
				
					$cart=$this->sessiondd->userdata("cart");
					$order_data["customer_id"]=(int)$cart["customer_id"];
					$email 	= $this->input->post("email");
					
					$data_address=array(
							 
							"firstname" 	=> $this->input->post("firstname"),
							"lastname"	=> $this->input->post("lastname"),
							"email" 	=> $this->input->post("email"),
							"company" 	=> $this->input->post("company"),
							"address_1" 	=> $this->input->post("address_1"),
							"address_2" 	=> $this->input->post("address_2"),
							"city_code" 	=> $this->account_model->get_city($this->input->post("city_code"))->row()->city_name ,
							"country_code" 	=> $this->account_model->get_country($this->input->post("country_code"))->row()->name ,
							"city" 		=> $this->input->post("city"),
							"postcode" 	=> $this->input->post("postcode")
					);
					$non_member=array(
							"firstname" 	=> $this->input->post("firstname1"),
							"lastname"	=> $this->input->post("lastname1"),
							"email" 	=> $this->input->post("email"),
							"telephone" => $this->input->post("telephone")
					);
					
					$order_data["address"]=serialize($data_address);
					$order_data["non_member"]=serialize($non_member);
			
				}
				
				
				$order_data["date_added"]=mktime();
				
				$order_data["order_status"]=$this->data["POST"]["order_status"];
				$order_data["order_note"]=$this->data["POST"]["order_note"];
				$order_data["shipping_status"]=1;
				if($this->data["cart_products"]["shipping"]){
					$order_data["shipping_company_id"]=$this->data["POST"]["shipping_company"];
					$order_data["shipping_price"]=$this->product_model->shipping_price($this->data["POST"]["shipping_company"]); 
				}else{
					$order_data["shipping_price"]=0;
				}
				
				$order_data["tax"]=$this->data["cart_products"]["totaltax"];
				
				$order_data["discount"]=$this->data["cart_summary"]["total_discount"];
				$order_data["total_pure"]=$this->data["cart_summary"]["total_pure"];
				
				if($this->data["cart_products"]["shipping"]){
					$order_data["total_all"]=$this->data["cart_summary"]["total_price"]+$order_data["shipping_price"];
				}else{
					$order_data["total_all"]=$this->data["cart_summary"]["total_price"];
				}
				
				$order_data["bank_id"]=$this->data["POST"]["order_status"]==1 ? $this->data["POST"]["bank_id"]:null;
				//dbg($order_data);exit;
				
				$this->db->insert("order",$order_data);
				
				$order_id=$this->db->insert_id();
				
				
				if($this->db->affected_rows()){
					
					foreach($this->data["cart_products"]["products"] as $product){
				
						$data_p["order_id"]		=$order_id;
						$data_p["customer_id"]	=$order_data["customer_id"];
						$data_p["product_id"]	=$product["product_id"];
						$data_p["basket_price"]	=$product["price"];
						$data_p["number"]		=$product["number"];
						$data_p["date_added"]	=mktime();
						$data_p["product_features"]	=$product["product_features"];
						$data_p["discount"]	=$product["discount"];
						
					$this->db->insert("order_product",$data_p);
					
					if($this->db->affected_rows()){
						$product_features=unserialize($product["product_features"]);
						if(! empty($product_features["features"])){
						
							$feature_level=array("product_feature_id","f1","f2","f3");
							
							$sql="update product_to_feature set number=number-".$product["number"]." where  product_id=".$product["product_id"];
							foreach ($product_features["features"] as $id=>$f_id ){
								$sql.=" and ".$feature_level[$product_features["level"][$id]]."=".$f_id;
							}
							
							$this->db->query($sql);
						}
					$this->db->query("update product set number=number-".$product["number"]." where pl_id=".$product["product_id"]);
					}
					
					}
					
					$this->data["order_made"]=$order_id;
				}
				
				/// plugin proccess starts here ///
				
				
				
					//$callback=true; //success  true
					$this->quick_model->logs("order id : ".$order_id." new order ");
					$this->product_model->cart_clear();
				
				
				
				//
				$this->load->helper('phpmailer');
				$this->load->library("email_template");
				
				$payment=array_column($this->data["payment_type"],"title","order_status_id");
				
				$data=array("template_id"=>2,"keys"=>$this->email_template->order($order_id));
				
				$this->email_template->set_template($data);
				
				$subject=$this->email_template->Template["subject"];
				$this->email_template->Template["subject"]=$subject ." ".$payment[$order_data["order_status"]];
				
				$this->email_template->SendMailWithGmailSMTP($email);
				
				$this->email_template->Template["subject"]=$subject." ".$payment[$order_data["order_status"]] ." ";
				$this->email_template->SendMailWithGmailSMTP($this->data["settings"]["email"]);
				
			}else {
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror);
				}
			}
		}else{
			$this->data["POST"]=$this->sessiondd->userdata("payment");
		}
   		
		$this->data["adress_link"]=$this->quick_model->get_link("/account/address");
   		
      	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
   
    	$this->smarty->view('cart',$this->data);
	}
	
	function add_to_cart(){
	
	$result["success"]="1";
	$result["message"]="1";
	$this->load->library('form_validation');
    
	$this->form_validation->set_rules('product_id', 'product_id', 'trim|required|xss_clean');	
	$this->form_validation->set_rules('number', 'Number', 'trim|required|xss_clean');	


	if( $this->input->is_ajax_request() ){
		

		if ($this->form_validation->run() 	) {
			
			if($this->quick->logged_user()){
				$logged_user=$this->quick->logged_user();
				$customer_id=$logged_user->customer_id;
			}else{
				$this->cart=$this->sessiondd->userdata("cart");
				$customer_id=$this->cart["customer_id"];
			}
			$p_price_info= $this->product_model->product_price($this->input->post("product_id"));
			
			$this->data["product"]=$this->db->where("product_id",$this->input->post("product_id"))->get("product");

			$data_insert=array(
					"number"=>$this->input->post("number"),
					"product_id"=>$this->input->post("product_id"),
					"customer_id"=>$customer_id ,
					"price"=>$p_price_info["price"] + $this->product_model->sum_feature_price($this->input->post("product_id"),$this->input->post("feature") ),
					"discount"=>$p_price_info["amount"],
					"date_added"=>mktime(),
					"product_features"=>serialize(array("features"=>$this->input->post("feature"),"level"=>$this->input->post("level")))
			);
			
	 		
			if($this->quick->logged_user()){	
	 	
				
		
				$data_where=array("product_id"=>$this->input->post("product_id"),"product_features"=>serialize($this->input->post("feature")) ,"customer_id"=>$logged_user->customer_id);
				
				$cart_p=$this->db->where($data_where)->get("cart");
			
				if($cart_p->num_rows>0){
				
					$this->db->where($data_where)->update("cart" ,array("number"=>$this->input->post("number") ));
			
					$result["message"]=$this->language_model->language_c_key("carttext3");//"Product Number successfuly changed !";
				}else{
				
					if($this->data["product"]->num_rows>0){
					
						$this->db->insert("cart",$data_insert);
						$result["message"]=$this->language_model->language_c_key("carttext2");//"Product Added Successfully !";
					}
				
				}
		
		}
		else {
			
			if($this->input->post("feature")){
				$key=$data_insert["product_id"]."_".implode("_",$this->input->post("feature"));
			}else{
				$key=$data_insert["product_id"];
			}
			$data_insert["cart_id"]=$key;                                         
			
			$this->cart["products"][$key]=$data_insert;
			
			$this->sessiondd->set_userdata('cart',$this->cart);
			
			//$result["success"]="3";
			$result["message"]=$this->language_model->language_c_key("carttext2");//"Product Added Successfully !";
			
		}
	}else{

		$result["success"]="0";
		$result["message"]= strip_tags(validation_errors());
		
	}
		
	}
	
	echo json_encode($result);	exit;
	}
	
	function change_number(){
	
		if( $this->input->is_ajax_request() ){
	
			if($this->quick->logged_user()){
				$this->db->where("cart_id",$this->input->post("cart_id"))->update("cart",array("number"=>$this->input->post("number")));
			}else{
				$this->cart=$this->sessiondd->userdata("cart");
				$this->cart["products"][$this->input->post("cart_id")]["number"]=$this->input->post("number");
	
				$this->sessiondd->set_userdata("cart",$this->cart);
			}
		}
		$result["success"]="1";
		$result["message"]=$this->language_model->language_c_key("carttext3");// "Number Changed Successfully";
		echo json_encode($result);	exit;
	}
	
	
	function delete_to_cart(){
	
	$result["success"]="1";
	$result["message"]="1";
	$this->load->library('form_validation');
    
    
	$this->form_validation->set_rules('id', '', 'trim|required|xss_clean');	
	

	if( $this->input->post() and $this->quick->logged_user() ){
	 	
	 	if ($this->form_validation->run() 	) { 
	
			$data_where=array("cart_id"=>$this->input->post("id"),"customer_id"=>$this->quick->logged_user()->customer_id);
			$cart_p=$this->db->where($data_where)->get("cart");
		
		
			if($cart_p->num_rows>0){
		
				$this->db->where($data_where)->delete("cart");
			$result["message"]=$this->language_model->language_c_key("carttext4");//"Product Removed!";
			
			}else{
			$result["success"]="0";
			$result["message"]="Product not in cart !";
			}
		
		}else {
			$result["success"]="0";
			$result["message"]= strip_tags(validation_errors());
		}
		
	}else{
		
		$this->cart=$this->sessiondd->userdata("cart");
		$key=$this->input->post("id");
		unset($this->cart["products"][$key]);
		
		$this->sessiondd->set_userdata('cart',$this->cart);
	}
	
	echo json_encode($result);	exit;
	}
	
	function change_language($id){
	
		$language=$this->language_model->language(array("language_id"=>$id,"status"=>1));
	
		if($language->num_rows){
			$_SESSION["cart21_language"]["language_id"]=$language->row()->language_id;
			$_SESSION["cart21_language"]["name"]=$language->row()->name;
			$_SESSION["cart21_language"]["short_name"]=$language->row()->short_name;
			
		}else{}
			
		echo '1'; exit;
	}
	
	function paymeny_type(){
		
		$meta = $this->quick_model->meta(array("type"=>1,"class_routes"=>"cart"));
		 
		if($meta->num_rows >0 ){
			$this->data["meta"]=$meta->row_array();
			// language load
			$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["m_id"]))->result_array(),"text_val","key_val");
			 
		}else{
		
		}


	
	if($this->input->post("key")=="banktransfer"){
		
			$this->data["banks"]=$this->quick_model->banks()->result_array();
		
			echo  $this->smarty->fetch('payment/bank.tpl',$this->data); exit;
		}elseif($this->input->post("key")=="paypal"){
		
			echo  $this->smarty->fetch('payment/paypal.tpl',$this->data); exit;
		}
		
		elseif($this->input->post("key")=="creditcart"){
			
			echo  $this->smarty->fetch('payment/creditcart.tpl',$this->data); exit;
		}elseif($this->input->post("key")=="payatthedoor"){
		
			$this->data["shipping_company"]=$this->product_model->shipping_companies();
		
			echo  $this->smarty->fetch('payment/shipping_company.tpl',$this->data); exit;
		}
		else{
		echo "dd"; exit;
		}
	
	return true;
	}
	
	function get_shipping_price(){
		
		$cart_product=$this->product_model->cart_products();
		$cart_summary=$cart_product["cart_summary"];
		
		$data["all_total"]=$cart_summary["total_price"];
		$data["ship_price"]=$this->product_model->shipping_price($this->input->post("company_id")); 
		
		echo json_encode($data);
		exit;
	}
	

	

	

	
	function dd(){
		
		//dbg($_SESSION);
	}
	
}