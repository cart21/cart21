<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class email_template {

	function __construct(){
	
		$this->CI =& get_instance() ;
		
		
		/// filter array ///
		
		$this->keys = array("dd"=>"");
		
		$this->Defaultkeys = array(
		
			"../imagesddddd"	=>"/imagesdddd",
			"../ddddddd"		=>"/"
		);
	
		
	}//construct
	
	
	function set_Template($data){

		$Template = $this->CI->db->query("SELECT *  FROM email_template WHERE et_id=".$data["template_id"]." and language_id=".f_language_id());
		if($Template ->num_rows<1){echo "template id is rigtht ";exit;}
		
		$this->Template=$Template->row_array();
		
		if(isset($data["keys"]) ){
			
			$this->keys=$data["keys"];
			$this->MakeKeys();
			$this->FilterTemplate();
		}
	}	
		
	function FilterTemplate(){
		
		foreach($this->Template as $key=>$value){
		$this->Template[$key]=  strtr($value,$this->keys); 
		}
		
		}	
		
	function MakeKeys(){
		
		foreach($this->keys as $key=>$value){
		
			unset($this->keys[$key]);
			
			if(is_array($value)){
				$this->MakeKeys2($value);
			}else{
				$key="{".$key."}";
	   			$this->keys[$key]=$value;
			}
		 
		}
		
		$this->keys=array_merge($this->keys,$this->Defaultkeys);
	}	
	
	function MakeKeys2($data){
		
		foreach($data as $key=>$value){
			unset($data[$key]);
			if(is_array($value)){
				
				$this->MakeKeys2($value);
			}else{
				
				
			$key="{".$key."}";
			if(!isset($this->keys[$key])){
				$this->keys[$key]=$value;
			}
			}
		}
	}
	
	function order($order_id){
	
		//$this->data["order"]=$this->CI->product_model->order($order_id)->row_array();
		
		$this->data["order"]=$this->CI->db->query("select * ,o.date_added as date_added from `order` as o
   
    			left join (select customer_id,firstname, lastname,email from customer) as c
    			on c.customer_id=o.customer_id
    
    		where o.order_id=".$order_id )->row_array();
		
		////
		$this->data["order"]["date_added"]=date("d-m-Y H:i",$this->data["order"]["date_added"]);
		if($this->data["order"]["address"]){
		$this->data["order"]["address"]=unserialize($this->data["order"]["address"]);
		$this->data["order"]["address"]["firstname2"]=$this->data["order"]["address"]["firstname"];
		$this->data["order"]["address"]["lastname2"]=$this->data["order"]["address"]["lastname"];
		 }
		
		$this->data["payment_type"]=$this->CI->product_model->order_status($this->data["order"]["order_status"])->row()->title;
		$this->data["shipping_status"]=$this->CI->product_model->order_shipping_status($this->data["order"]["shipping_status"])->row()->title;
		
		if($this->data["order"]["order_status"]=="1"){
		$this->data["bank"]=$this->CI->product_model->bank($this->data["order"]["bank_id"])->row()->bank_name;
		}else{
			$this->data["bank"]="";
		}
		
		if (isset($this->data["order"]["shipping_company_id"])){
		$this->data["shipping_company"]=$this->CI->product_model->shipping_company($this->data["order"]["shipping_company_id"])->row()->company_name;
		}else{
			$this->data["shipping_company"]="";
		}
		 
		if($this->data["order"]["non_member"]){
			$this->data["order"]["non_member"]=unserialize($this->data["order"]["non_member"]);
		}
		
		////
		
		/// Products
		$this->data["order_products"]=$this->CI->db->query("
				select * from order_product as op
    			left join product as p
    			on p.product_id=op.product_id
    
    			left join ( select product_id,image_loc from product_image group by product_id  )as pi
    	on pi.product_id=op.product_id
    
    		where op.order_id=".$order_id)->result_array();
		
		$this->data["products"]= '<table style="width: 99%;"><tr>
						          <td><b>'.$this->CI->language_model->language_c_key("product").'</b></td>
						          <td><b>'.$this->CI->language_model->language_c_key("price").'</b></td>
						          <td><b>'.$this->CI->language_model->language_c_key("piece").'</b></td>
						          <td><b>'.$this->CI->language_model->language_c_key("total").'</b></td>
						        </tr>';
		
		$this->data["total_with_tax"]=0;
		foreach( $this->data["order_products"] as $p ){
		
			$this->data["total_with_tax"] +=$p["basket_price"]*$p["number"];
			
			if($p["discount"]>0){
				 $price=  $p["basket_price"]-$p["discount"] ." <del style='color:red;'>  ".$p["basket_price"]." </del>";
			}else{
				$price=$p["basket_price"];
			}
			$this->data["products"].= '<tr>
						          <td> <a href="'.base_url().$p["slug"].'" >'. $p["title"] .'</td>
						          <td>'. $price .'</td>
						          <td>'. $p["number"] .' </td>
						          <td>'. $p["number"]*$price .' </td>
						        </tr>';
		}
		
		
		
		$this->data["products"].='</table>';

		
		unset($this->data["order_products"]);
	return $this->data;
	}
		
	function SendMailWithGmailSMTP($to){
	
	
	SendMailWithGmailSMTP($to,$this->Template["subject"], $this->Template["body"]);
	}
/// CLASS END
}



?>