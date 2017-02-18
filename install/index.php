<?php
//error_reporting(E_ERROR);
class install  {
 
    function __construct() {
    	require_once($_SERVER["DOCUMENT_ROOT"].'/application/front/libraries/smarty/libs/Smarty.class.php');
    	
    	$this->smarty= new Smarty();
    	
    	$this->smarty->setTemplateDir($_SERVER["DOCUMENT_ROOT"].'/install/templates/');
    	$this->smarty->setCompileDir($_SERVER["DOCUMENT_ROOT"].'/install/templates_c/');
    	$this->smarty->setConfigDir('/application/front/libraries/smarty/configs');
    	$this->smarty->setCacheDir('/application/front/libraries/smarty/cache');
    	
    	//echo '<pre>';print_r($this->smarty);echo '<pre>';
    }
    
    function view($template_name,$data = array() ) {
    
    	foreach ($data as $key => $val)
    	{
    		$this->smarty->assign($key, $val);
    	}
    
    	if (strpos($template_name, '.') === FALSE && strpos($template_name, ':') === FALSE) {
    		$template_name .= '.tpl';
    	}
    	$this->smarty->display($template_name);
    }
    
    function index() {
    	$this->data["errors"]="";
    	$this->data["success"]="";
    	
    	if( $_POST ){
    		
    		$check_post=true;
    		
    		$this->data["POST"]=array_filter($_POST);
    		
    		if( !isset($this->data["POST"]["title"]) or $this->data["POST"]["title"]==""){
    			$check_post=false;
    			$this->data["errors"][]="Website title be empty !";
    		}
    		/*
    		$regex = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
    		if(!isset($this->data["POST"]["email"]) or !preg_match( $regex,$this->data["POST"]["email"])){
    			$check_post=false;
    			$this->data["errors"][]="Email is wrong";
    		}
    		
    		if(!isset($this->data["POST"]["admin_pass"]) or $this->data["POST"]["admin_pass"]==""){
    			$check_post=false;
    			$this->data["errors"][]="Admin password cannot be empty !";
    		}
    		*/
    		if(!isset($this->data["POST"]["host"]) or $this->data["POST"]["host"]==""){
    			$check_post=false;
    			$this->data["errors"][]="host cannot be empty !";
    		}
    		
    		if(!isset($this->data["POST"]["user"]) or $this->data["POST"]["user"]==""){
    			$check_post=false;
    			$this->data["errors"][]="database user cannot be empty !";
    		}
    		
    		if(!isset($this->data["POST"]["pass"]) or $this->data["POST"]["pass"]==""){
    			//$check_post=false;
    			//$this->data["errors"][]="Database password cannot be empty !";
    			//$this->data["POST"]["pass"]="";
    		}
    		
    		if(!isset($this->data["POST"]["database"]) or $this->data["POST"]["database"]==""){
    			$check_post=false;
    			$this->data["errors"][]="database name cannot be empty !";
    		}
    		
    		
    		if ($check_post ){
    			
    			$link = mysqli_connect($this->data["POST"]["host"],$this->data["POST"]["user"], $this->data["POST"]["pass"], $this->data["POST"]["database"]) ;
    			
    			/* check connection */
    			if (mysqli_connect_errno()) {
    				$this->data["errors"][]= mysqli_connect_error();
    				
    			}else{
    			
    				$query=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/install/cart21.sql");
    				
    				$query.="
    						update `settings_options`

							set  `site_title`='{$this->data["POST"]["title"]}',

									`site_url`='http://".$_SERVER["HTTP_HOST"]."/'

								where site_url like '%localhost%'
											
											";
    				
    			
    			
    				mysqli_multi_query($link, $query);
    				
    					sleep(6);
    					
    				//$link->query();
    				
    				/// file operation ///
					
					$moification[]=array(
    				
    				"find"=>"\$db['default']['hostname'] = 'localhost';",
    				"replace"=>"\$db['default']['hostname'] = '{$this->data["POST"]["host"]}';"
    						
    				);
    				$moification[]=array(
    				
    				"find"=>"\$db['default']['username'] = 'root';",
    				"replace"=>"\$db['default']['username'] = '{$this->data["POST"]["user"]}';"
    						
    				);
    				$moification[]=array(
    				
    				"find"=>"\$db['default']['password'] = '';",
    				"replace"=>"\$db['default']['password'] = '{$this->data["POST"]["pass"]}';"
    						
    				);
    				$moification[]=array(
    				
    						"find"=>"\$db['default']['database'] = 'cart21demo';",
    						"replace"=>"\$db['default']['database'] = '{$this->data["POST"]["database"]}';"
    				
    				);
    					
    				
    				
    				foreach ($moification as $m){
    						
    					$config_front=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/application/front/config/database.php");
    					$config_admin=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/application/admin/config/database.php");
    					
    					if( preg_match('^'.preg_quote($m["find"]).'^', $config_front) ){
    						
    						$new_front=preg_replace('^'.preg_quote($m["find"]).'^' , $m["replace"], $config_front) ;
    						file_put_contents($_SERVER["DOCUMENT_ROOT"]."/application/front/config/database.php", $new_front);
    						
    					}elseif(! preg_match('^'.preg_quote($m["replace"]).'^', $config_front)){
    						$result["result"]=false;
    						$this->data["errors"][]="plugin modification not found in front database.php <br>".htmlspecialchars($m["find"]);
    					}
    					
    					if( preg_match('^'.preg_quote($m["find"]).'^', $config_admin)){
	    				
    						$new_admin=preg_replace('^'.preg_quote($m["find"]).'^' , $m["replace"], $config_admin) ;
    						file_put_contents($_SERVER["DOCUMENT_ROOT"]."/application/admin/config/database.php", $new_admin);
    						
    					}elseif(! preg_match('^'.preg_quote($m["replace"]).'^', $config_admin)){
    						$result["result"]=false;
    						$this->data["errors"][]="plugin modification not found in  admin database.php <br> <br>".htmlspecialchars($m["find"]);
    					}
    				
    				}

    				
    				if($this->data["errors"]==""){
    				
    				/* close connection */
    				mysqli_close($link);
    			
    				$dir=$_SERVER["DOCUMENT_ROOT"].'/install';
    				//rmdir($_SERVER["DOCUMENT_ROOT"].'/install');
    				//system("rm -rf ".escapeshellarg($dir));
    				
    				header("Location: http://".$_SERVER["HTTP_HOST"]);
    			
    				}
    			}
    				
    	
    	}else {
    		}
    	
    	}
    	
    	$this->view('index.tpl',$this->data);
    	}
    	
    	function installation_controll($modification,$action){
    	
    		$result["result"]=true;
    	
    		foreach ($modification as $key=>$file){
    				
    			
    			$file["filename"]=$_SERVER["DOCUMENT_ROOT"]."/application/front/config/database.php";
    			//
    			$content=file_get_contents($file["filename"]);
    				
    			if ($action=="install"){
    	
    			if(! preg_match('^'.preg_quote($file["find"]).'^', $content)){
    				
    			$result["result"]=false;
    					$result["error_message"][]="plugin modification not found in ".$file["filename"]."<br>".htmlspecialchars($file["find"]);
    			}
    			}else{
    	
    			if(! preg_match('^'.preg_quote($file["plugin"]).'^', $content)){
    	
    					$result["result"]=false;
    				$result["error_message"][]="plugin modification not found in ".$file["filename"]."<br>".htmlspecialchars($file["plugin"]);
    			}
    			}
    				
    		}
    	
    		return $result;
    		}
    	
}

$install_class= new install();

$install_class->index();