<?php
 include(APPPATH."hooks/hooking.php");
class settings_options extends hooking {
   

	function under_construction(){
	
	 include(APPPATH."config/config.php");
	    $settings_options=$this->getTable("settings_options");
	    $URI=explode('/',$_SERVER["REQUEST_URI"]);
	    if($settings_options->row["offline"]=="1" and "under_construction"!=$URI[1] and !isset($_SESSION["admin"]) ){
	    
	    header("location:".$config['base_url']."under_construction");
	    }
	    

    	}  
    	
    function language(){
	
		 include(APPPATH."config/config.php");
		 $CI= & get_instance();
		
		 $settings=$this->getTable("settings_options");
		 
		 $default_language=$this->getTable("language",array("language_id"=>$settings->row["admin_language"]));
		 
		 if(isset($_SESSION["cart21_a_language"])){
		 	
		 	$CI->config->set_item('language',$_SESSION["cart21_a_language"]["name"]);
		 }else{
		 	
		 	$CI->config->set_item('language',$default_language->row["name"]);
		 	
		 	$_SESSION["cart21_a_language"]=array(
		 			"language_id"=>$default_language->row["language_id"],
		 			"name"=>$default_language->row["name"],
		 			"short_name"=>$default_language->row["short_name"],
		 			"image"=>$default_language->row["image"]
		 	);
		 	
		 }
	
    }
    
    function set_settings_options(){
		 
		$CI= & get_instance();
		
		$CI->data["settings"]=$settings=$this->specialquery("select * from settings_options where site_url like '%".$_SERVER["SERVER_NAME"]."%'")->row;
		$CI->data["settings"]["template_dir"]=$CI->smarty->joined_template_dir;
		
		$CI->config->set_item('base_url',$settings["site_url"]);
		
		
    }
    
    
}