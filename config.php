<?php 

	$segment=explode('/',$_SERVER["REQUEST_URI"]);
	
	if(count($segment)>1 && $segment[1]=="admin" ){
		if(  $segment[1]=="admin" ){
			$application_folder = 'application/admin';
		}else if(isset($_SESSION["admin"]) && $segment[1]=="admin" ){
			$application_folder = 'admin';
		}else{
			$application_folder = 'application/front';
		}
	
	}else{
		$application_folder = 'application/front';
	}
	


?>
