<?php
class product_module extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    	
    	
    	////
    	$param["settings"]=array(
    			"none"=>0
    			
    	);
    	
    	 $data=array(
    	 		"title"=>"Product Module",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>2,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"product_module", /// same as library file name
    	 		"content"=>"show the product in a page position",
    	 		"status"=>0,
    	 		"positional"=>0,
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
