<?php

class mysupport extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        $this->modules->account_left();
       
    }
 
    function index() {

		$customer=$this->sessiondd->userdata('customer');
		$this->smarty->assign("username",$customer["firstname"] );
        
        $this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
        if($this->quick->logged_in()) {
       		$this->smarty->view('support/support',$this->data);	
		}else{
			redirect($this->quick_model->get_link("account/login"));
        }
        
    }
	
	function support() {

		if(! $this->quick->logged_in()) {
			redirect($this->quick_model->get_link("mysupport/feedback"));
		}
		
		$customer=$this->sessiondd->userdata('customer');
		$this->smarty->assign("username",$customer["firstname"] );
        $where=array("user_id"=>$customer["customer_id"],"top_id"=>0);
        $MYSUPPORTS=$this->db->order_by("support_id","desc")->where($where)->get("support");
        
        if($MYSUPPORTS->num_rows>0){
        foreach($MYSUPPORTS->result_array() as $MYSUPPORT){
        
       	 $MYSUPPORT["date_added"]=date("d/m/Y H:i",$MYSUPPORT["date_added"]);
       	 $MYSUPPORT["customer"]=$this->db->where("customer_id",$customer["customer_id"])->get("customer")->row_array();
        $MYSUPPORTS2[]=$MYSUPPORT;
        }
        
        $this->data["MYSUPPORTS"]=$MYSUPPORTS2;
        
        }else{
        $this->data["MYSUPPORTS"]=null;
        }
        
        $this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
        if($this->quick->logged_in()) {
        
       		$this->smarty->view('support/support' ,$this->data);	
		}else{
			redirect($this->quick_model->get_link("account/login"));
        }
        
    }
    
    function feedback() {
    
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    
    	$this->smarty->view('index' ,$this->data);
    
    }
    
    function submitaticket() {
    
    	$this->quick->onlyLoginUser();
    	
    	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
     	if(  $this->input->post() && $this->quick->logged_in()  ){
     	
     	//$this->quick->dbg2($this->input->post());
     	$data_support=array(
	        	"subject" 	=> $this->input->post("title"),	
	        	"category_id"	=> $this->input->post("category"),
	        	"message" 	=> $this->input->post("message"),
	        	"user_id" 	=> utf8_encode($this->quick->logged_customer()->customer_id),
	        	"customer_id" 	=> utf8_encode($this->quick->logged_customer()->customer_id),
	        	"top_id" 	=> $this->input->post("top_id"),
	        	"date_added" 	=> mktime()
	        	
	        	
        	);
     	
if($_FILES["attachment"]["error"]!=4){
        $config['upload_path'] ='./uploads/support/';
		//$config['file_name'] = mktime()+rand(1,5);
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload("attachment"))
		{
			$error = $this->upload->display_errors();
			$this->quick->error[]=$error;
			//dbg2($error);
			//$this->load->view('upload_form', $error);
		}
		else
		{
			$file_data =  $this->upload->data();
			//dbg2($this->upload->data());
			$data_support["attachment"]="uploads/support/".$file_data['file_name'];

		}
		
	}
		
	$this->db->insert("support",$data_support);
	
	if($this->input->post("top_id")!=0){	
	
     	redirect('mysupports.html/ticket/'.$this->input->post("top_id"));
     	}else{
     	
     	redirect($this->quick_model->get_link("/mysupport/support") );
     	}
     	}
     	
        if($this->quick->logged_in()) {
        
        	$customer=$this->sessiondd->userdata('customer');
		$this->smarty->assign("username",$customer["firstname"] );
        
       		$this->smarty->view('support/submitaticket',$this->data);	
	}else{
		redirect($this->quick_model->get_link("account/login"));
        }
        
    }
    
    function ticket($top_id) {
    
    	$this->quick->onlyLoginUser();
    	
       	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
        $meta = $this->quick_model->meta(array("type"=>1,"class_routes"=>"/mysupport/support"));
         
        if($meta->num_rows >0 ){
        	$this->data["meta"]=$meta->row_array();
        	// language load
        	$this->data["L"]=array_column($this->language_model->languga_c_by_page(array($this->data["meta"]["meta_id"]))->result_array(),"text_val","key_val");
        	 
        }else{
        
        }
        
        
       $customer=$this->sessiondd->userdata('customer');
       $this->data["username"]=$customer["firstname"];
       
       $TICKETS=$this->db->order_by("support_id","desc")
       ->where("customer_id", $customer["customer_id"])
       ->where("(top_id=".$top_id."  OR  support_id=".$top_id.")" )
       ->get("support");
       
       $this->data["TOTAL"]=$TICKETS->num_rows;
       
       if($this->data["TOTAL"]>0){
        foreach($TICKETS->result_array() as $TICKET){
        
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
       }else{
       
       
       }
       
        
        if($this->quick->logged_in()) {
       		$this->smarty->view('support/ticket',$this->data);	
		}else{
			redirect($this->quick_model->get_link("account/login"));
        }
        
    }
   
    function documentation(){
    
    	$this->quick->onlyLoginUser();
     
      	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer(""); 
        
     $this->smarty->view('support/Documentation',$this->data);	
     
     }
     
    function info(){
     
     
      	$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
        
        
       		$this->smarty->view('support/support_info',$this->data);	
     
    }
		
}