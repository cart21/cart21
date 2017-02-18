<?php
class social_link extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    	
    	
    	////
    	$param["settings"]=array(
    			"float"=>"left",
    			"height"=>"28"
    			
    	);
    	
    	 $data=array(
    	 		"title"=>"Social Links",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>8,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"social_link", /// same as library file name
    	 		"content"=>"Social Links",
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
