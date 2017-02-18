<?php
class latest_products_v2 extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    
    	////
    	$param["sample"]=array(
    			"limit"=>"6",
    			"column"=>4
    	);
    	
    	
   
    	 $data=array(
    	 		"title"=>"Latest product V2",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>8,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"latest_products_v2", /// same as library file name
    	 		"content"=>"Latest product on left menu under the news limit on the option ",
    	 		"status"=>0,
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
		echo "already installed ! new files uploaded <a href='/admin/plugin'>All plugins</a>";
		}
		
		
    }
    
}
