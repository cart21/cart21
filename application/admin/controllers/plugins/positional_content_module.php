<?php
class positional_content_module extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('plugin_model');
        
    }
 
    function index(){
    	
    	
    	////
    	$param["settings"]=array(
    			"width"=>"100%"
    			
    	);
    	
    	$this->db->query("CREATE TABLE IF NOT EXISTS `positional_content` (
  `positional_content_id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`positional_content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
    	
    	
    	//// SECTIN INFO ////
    	
    	$sections[]=array(
    	
    			"name"=>"positional_content",
	    		"class"=>"positional_content",
    			"file_name"=>"positional_content.php"
    	
    	);
    	
    	foreach($sections as $m){
    	
    		if(! $this->db->get_where("permission_sections",array("name"=>"download_release"))->num_rows ){
    	
    			$this->db->insert("permission_sections",$m);
    	
    			echo "new section added";
    	
    		}else{
    	
    			echo "new section already exist";
    	
    		}
    	
    	}
    	
    	//// SECTIN INFO ////
    	
    	
    	 $data=array(
    	 		"title"=>"positional_content_module",
    	 		"publisher"=>"cart21",
    	 		"plugin_type_id"=>8,
    	 		"pub_email"=>"support@cart21.com",
    	 		"p_key"=>"positional_content_module", /// same as library file name
    	 		"content"=>"positional_content_module",
    	 		"status"=>0,
    	 		"manage_link"=>"admin/positional_content",
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
