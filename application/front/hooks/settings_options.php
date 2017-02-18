<?php
 include(APPPATH."hooks/hooking.php");
class settings_options extends hooking {

	function under_construction(){
	
		 include(APPPATH."config/config.php");
		    $settings_options=$settings=$this->specialquery("select * from settings_options where site_url like '%".$_SERVER["HTTP_HOST"]."%'");
		    
		    $URI=explode('/',$_SERVER["REQUEST_URI"]);
		    
		    if(  isset($_SESSION["admin"]) ){
		    
		    }elseif($settings_options->row["offline"]=="1" and "under_construction"!=$URI[1]  ){
		    
		    header("location:".$config['base_url']."under_construction");
		    }else{
		    
		    }
	 
  	}
  	  
    function language(){
    
		 include(APPPATH."config/config.php");
		 $CI= & get_instance();
		
		 $settings=$this->getTable("settings_options");
		 
		 $default_language=$this->getTable("language",array("language_id"=>$settings->row["front_language"]));
		 
		 if(isset($_SESSION["cart21_language"])){
		 	
		 	$CI->config->set_item('language',$_SESSION["cart21_language"]["name"]);
		 }else{
		 	
		 	$CI->config->set_item('language',$default_language->row["name"]);
		 	
		 	$_SESSION["cart21_language"]=array(
		 			"language_id"=>$default_language->row["language_id"],
		 			"name"=>$default_language->row["name"],
		 			"short_name"=>$default_language->row["short_name"]
		 	);
		 	
		 	$_SESSION["cart21_cf_language"]=$_SESSION["cart21_language"];
		 	
		 }
		 
	 
		 if(! isset($_SESSION["cart"]["customer_id"])){
		 	
		 	$_SESSION["cart"]["customer_id"]=rand()+strtotime("now");
		 }
	
    }
    
     function set_settings_options(){
		$CI= & get_instance();
		
		$CI->data["settings"]=$settings=$this->specialquery("select * from settings_options where site_url like '%".$_SERVER["HTTP_HOST"]."%'")->row;
	
		$CI->smarty->joined_template_dir=$CI->data["settings"]["front_theme"];
		
		$CI->data["settings"]["template_dir"]=$CI->smarty->joined_template_dir;
		
		$CI->config->set_item('base_url',$settings["site_url"]);
		
		
		///
		if(!isset($_SESSION["cart21_currency"])){
		$default_currency=$this->getTable("currency",array("currency_id"=>$CI->data["settings"]["currency_id"]));
		
		$CI->data["settings"]["currency"]=array(
				"currency_id"=>$default_currency->row["currency_id"],
				"name"=>$default_currency->row["name"],
				"short_name"=>$default_currency->row["short_name"],
				"sign"=>$default_currency->row["sign"],
				"rate"=>$default_currency->row["rate"]
		);
		
		$_SESSION["cart21_currency"]=$CI->data["settings"]["currency"];
		}else{
			 $CI->data["settings"]["currency"]=$_SESSION["cart21_currency"];
		}
				
	
     		///
    }
    
    
 
}

