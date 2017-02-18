<?php
class product extends CI_Controller {
 
    function __construct() {
        parent::__construct();
        
        $this->load->model("product_model");
    }
 
    function index($product_id=10) {
		$this->data["product"]=$this->product_model->product($product_id)->row_array();
		$this->redirect_language();
		
		$this->data["product"]["price_d"]=$this->product_model->product_price($product_id);
   		$this->data["product_images"]=$this->product_model->product_image($this->data["product"]["pl_id"]);
   		
   		$this->data["product_features"]=$this->product_model->get_product_feature_tree($this->data["product"]["pl_id"],$this->data["product"]["language_id"],'0');
   		$this->data["product_features_selected"]=$this->product_model->get_product_feature_tree($this->data["product"]["pl_id"],$this->data["product"]["language_id"],'1');
   		
       	$product_comments=$this->product_model->product_comments($product_id);
       	$this->data["product_comments_num"]=$product_comments->num_rows();
       	
       	$this->data["product_comments"]=$this->data["product_comments_num"] >0 ? $product_comments->result_array() : "No Comment Made Yet !";
       	
       	$this->data["properties"]=$this->product_model->properties($this->data["product"]["pl_id"],$this->data["product"]["language_id"]);
       	$this->data["brands"]=$this->product_model->brands($this->data["product"]["pl_id"],$this->data["product"]["language_id"]);
       	
       	$this->data["product_related"]=$this->product_model->product_related($this->data["product"]["pl_id"]);
       	
        $this->data["product_category"]=$this->product_model->product_categories_p($this->data["product"]["pl_id"],$this->data["product"]["language_id"]);
     
        $this->db->query("update product set view_count=view_count+1 where pl_id=".$this->data["product"]["pl_id"]); 
        
   		$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
     
       
       
       $this->smarty->view("product",$this->data);
 	}
 	
 	
 	function product_comment($product_id){
 	
 	
 		if($this->input->post()){
 		
			$this->data["POST"]= $this->input->post();

			$this->product=$this->product_model->product_opt(array("language_id"=>f_language_id(),"pl_id"=>$product_id));
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('comment', 'Message', 'trim|required|xss_clean');
			if( $this->form_validation->run() ){
		 
			$this->db->insert("product_comment",array("comment"=>$this->input->post("comment"),"meta_type_id"=>4,"product_id"=>$product_id,"customer_id"=>$this->quick->logged_customer()->customer_id));
			
			$this->load->library('CIphpmailer');
    	
        	$text_message="";
			$text_message.='<p> comment :'.$this->input->post("comment").'</p>';
			
			$r=$this->ciphpmailer->SendMailWithSMTP($this->data["settings"]["email"],'new comment made produc_id:'.$product_id ,$text_message,$From="");	
    		
    		if($r){
    			$this->quick->success[] = $this->language_model->language_c_key("producttext3");
				$this->data["POST"]="";
    		}else{
    		$this->quick->errors[] =$this->ciphpmailer->phpmailer->ErrorInfo;
    		}
			
			
			}else {
				$verrors=array_filter(explode('.',validation_errors()));
				foreach($verrors as $verror){
					$this->quick->errors[] = strip_tags($verror).".";
				}
			}
		
 		
 		}
 		
 		
 		redirect($this->product->row()->slug);
 		
 			
 	}
 	

 	function product_sendtofriend($product_id){
 	
 	}
 	
    function redirect_language(){
    
    	if($this->data["product"]["language_id"]!=$_SESSION["cart21_language"]["language_id"]){
    	
    		$redirect=$this->product_model->product_opt(array(
    				"language_id"=>$_SESSION["cart21_language"]["language_id"],
    				"pl_id"=>$this->data["product"]["pl_id"]
    		));
    		if($redirect->num_rows ){
    			redirect(base_url().$redirect->row()->slug);
    			exit;
    		}
    		else{
    			//redirect("404");
    			//exit;
    		}
    	}
    	
    }
    
    function filter_sub_features(){
    	$this->load->model("product_model");
    	
    	$result=$this->product_model->product_to_features($this->input->post("product_id"),$this->input->post("sub"),f_language_id(),"0",$groupping=false);
    //	dbg($this->db->last_query());
    	if($result->num_rows){
    	
	    	$data["options"]=null;
	    		
	    		$feature_level=array("product_feature_id","f1","f2","f3");
	    		$f_ids=explode("-",$this->input->post("key"));
	    		//dbg($result->result_array() );
	    	foreach ($result->result_array() as $v){
	    		$ok =0;
	    		
	    		for ($i = 0; $i <=$this->input->post("feature_level"); $i++) {
	    			
		    		if ($v[$feature_level[$i]] == $f_ids[$i]){
		    			
		    			$ok++; 
		    		}
	    		}
	    		
	    		if($ok==$this->input->post("feature_level")+1){ 
	    			$unique_key=implode("-",array_slice( explode("-",$v["fkey"]), 0, ($this->input->post("feature_level")+2) ));
	    			$data["options"][$unique_key]=$v; 
	    		
	    		}
	    	
	    	}
	   
	    	
    	}else{
    		$data["options"]=null;
    	}
    	//dbg($data["options"]);
    echo json_encode($data); exit;
    	
    }

    
   
}