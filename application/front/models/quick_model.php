<?php
class quick_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
         
    }
    
  
   function logs($message){
   
	   	if($this->quick->logged_user()){
	   		$logged_user=$this->quick->logged_user();
	   		$customer_id=$logged_user->customer_id;
	   	}else{
	   		$this->cart=$this->sessiondd->userdata("cart");
	   		$customer_id=$this->cart["customer_id"];
	   	}
	   
	   $logs_data["user_id"]=$customer_id;
	   $logs_data["user_type"]=1;
	   
	   $logs_data["date_added"]=mktime();
	   $logs_data["message"]=$message;
	   
	$this->db->insert("admin_logs",$logs_data);
	SendMailWithGmailSMTP($this->data["settings"]["email"],  $logs_data["message"],$logs_data["message"]);
   }
   
   function banks(){
   
   return $this->db->get("bank");
   }
   
   function get_blog_category($object_id){
   
	$sql="SELECT * 
	FROM wp_term_relationships AS tpr
	LEFT JOIN wp_terms AS t ON t.term_id = tpr.`term_taxonomy_id` 
	WHERE tpr.object_id =".$object_id;
	
	return $this->db->query($sql);
   
   }
   
   
    function meta($data){
    	$this->db->where("language_id",f_language_id());
  
    $m=$this->db->where($data)->get("meta");
 
    return $m;
    }
    
    function get_link($class_route,$language_id=null){
    
    	if(! $language_id){
    		$language_id=f_language_id();
    	}
    	 
    	$this->db->where("language_id",$language_id);
    	$meta=$this->db->where("class_routes",$class_route)->get("meta");
    	 
    	if($meta->num_rows){
    		return $meta->row()->link;
    	}else{
    		return "";
    	}
    }
    
    
    function meta_non_lang($data){
    	
    	$m=$this->db->where($data)->get("meta");
    
    	return $m;
    }
    
    function footer_link($language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
    
     	$this->db->where("language_id",$language_id);
    	return $this->db->order_by("foot_sort","asc")->where("footer",1)->get("meta");
    }
     
    function top_link($language_id=null){
    
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_language"]["language_id"];
    	}
    	$this->db->where_in("language_id",$language_id);
    	 
    	return $this->db->order_by("top_sort","asc")->where("top_menu",1)->get("meta");
    }
     
    function social_links(){
    	 
    	return $this->db->where("status",1)->get("social_link");
    }
   
   
}