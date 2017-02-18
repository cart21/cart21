<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class quick_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
      
    }
    
   function logs($message){
   
	   $admin=$this->quick->get_admin();
	   
	   $logs_data["user_id"]=$admin ? $admin["customer_id"] : 0;
	   $logs_data["date_added"]=mktime();
	   $logs_data["message"]=$message;
	   $logs_data["user_ip"]= $_SERVER["REMOTE_ADDR"];
	   $logs_data["user_agent"]= $_SERVER["HTTP_USER_AGENT"];
	   
	$this->db->insert("admin_logs",$logs_data);
	$body="";
	foreach ($logs_data as $k=>$v){
		
		if($k=="date_added"){
			$v=date("d-m-Y H:i:s",$v);
		}
		$body.=$k." ".$v ."<br>";
		
	}

	//SendMailWithGmailSMTP($this->data["settings"]["email"],  $logs_data["message"],$body);
   }
   
   function get_blog_category($object_id){
   
	$sql="SELECT * 
	FROM wp_term_relationships AS tpr
	LEFT JOIN wp_terms AS t ON t.term_id = tpr.`term_taxonomy_id` 
	WHERE tpr.object_id =".$object_id;
	
	return $this->db->query($sql);
   
   }
   
   function get_sites(){
   	
   	return $this->db->get("settings_options");
   }
   
   function get_language(){
   	
   return $this->db->get("language");
   }
   
   function footer_link($language_id=null){
    	if(! $language_id){
    		$language_id=language_id();
    	}
    	
    	$this->db->where(array("language_id"=>$language_id));
   	 
   	return $this->db->order_by("foot_sort","asc")->where("footer",1)->get("meta");
   }
   
   function top_link($language_id=null){
    	if(! $language_id){
    		$language_id=language_id();
    	}
    	
    	$this->db->where(array("language_id"=>$language_id));
   	return $this->db->order_by("top_sort","asc")->where("top_menu",1)->get("meta");
   }

   function social_links(){
   	 
   	return $this->db->get("social_link");
   } 
   
   function currencies(){
   	    
   	return $this->db->get("currency");
   }
   
   function et_group($et_id){
   
   	$sql="SELECT
    			l.image as image,
    			l.language_id as language_id,
    			et.et_id as et_id,
    			et.email_template_id as email_template_id
   
    	FROM `language` as l
   
		left join email_template as et
		on et.language_id=l.language_id and et_id=".$et_id."
   
		where l.status=1 ";
   
   	return $this->db->query($sql);
   }
   
   
   
}