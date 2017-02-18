<?php
class latest_content extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    	
    	
    	////
    	$param["default"]=array(
    			"limit"=>10
    			
    	);
    	
    	 $data=array(
    	 		"title"=>"Latest Content",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>8,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"latest_content", /// same as library file name
    	 		"content"=>"Latest Content",
    	 		"status"=>0,
    	 		"positional"=>1,
    	 		"param"=> serialize($param)

    	 );
    	 ///
		$check_plugin=$this->db->where("p_key",$data["p_key"])->get("plugin");
		
		if($check_plugin->num_rows==0 ){
    	 
			$this->db->insert("plugin",$data);
    	 	$plugin_id=$this->db->insert_id();
    	 	
    	 redirect("admin/plugin");
		}else{
		
		echo "already installed ! new files uploaded <a href='/admin/plugin'>All Plugins</a>";
		}
	
		
    }
    
}
