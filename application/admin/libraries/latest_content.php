<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class latest_content {
 
    function __construct(){
       
    	$this->CI =& get_instance();
    	
    }
    
    function install($action){
    	
    	$modification[]=array(
    			 
    			"action"=> $action,
    			"filename"=>'application/front/models/content_model.php',
    			"type"=>"before",
    			 
    			"find"=>'function contents(',
    	
    			"plugin"=>'
    	
    /// plugin latest_content ///
    function latest_content($limit=10,$language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
     	$this->db->where("language_id",$language_id);
    
    return $this->db->where("status",1)->order_by("content_id","desc")->limit($limit)->get("content");
    }
	/// plugin latest_content ///
    
    			'
    	);
    	
    	 
    	return $this->CI->modify_file($modification,$action);
    }
    
  
  
    
}

?>