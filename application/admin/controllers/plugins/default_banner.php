<?php
class default_banner extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    	
    	
    	////
    	$param["settings"]=array(
    			"width"=>"100%"
    			
    	);
    	
    	 $data=array(
    	 		"title"=>"Banner",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>8,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"default_banner", /// same as library file name
    	 		"content"=>"Default Banner",
    	 		"status"=>0,
    	 		"manage_link"=>"admin/banner",
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
			$this->db->where("p_key",$data["p_key"])->update("plugin",$data);
		echo "already installed ! new files uploaded <a href='/admin/plugin'>All Plugins</a>";
		}
	
		
    }
    
}
