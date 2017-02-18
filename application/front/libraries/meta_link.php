<?php
class meta_link {


	function __construct(){
		 $this->CI =& get_instance() ;
		 
		//self::dedect_link();
	}
	
	function dedect_link(){
	
	//echo $_SERVER["REQUEST_URI"] ;
	
	$link=$this->CI->db->get_where("meta",array("link"=>$_SERVER["REQUEST_URI"]));
	
	if($link->num_rows==0){
	$this->CI->db->insert("meta", array("link"=>$_SERVER["REQUEST_URI"])  );
	}
	//dbg2($link);
	}
	
	
	function link_exist($link){
	
		$meta=$this->CI->db->get_where("meta",array("link"=>$link));
	
		if($meta->num_rows==0){
		return false;
		}else{
		return true;
		}
	
	}
	
	
	function sluggify($title){
	
	
    # Prep string with some basic normalization
    $title = strtolower($title);
    $title = strip_tags($title);
    $title = stripslashes($title);
    $title = html_entity_decode($title);

    # Remove quotes (can't, etc.)
    $title = str_replace('\'', '', $title);

    # Replace non-alpha numeric with hyphens
    $match = '/[^a-z0-9]+/';
    $replace = '-';
    $title = preg_replace($match, $replace, $title);

    $title = trim($title, '-');

    return $title;
	}
	
	
	function toAscii($str) {
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

	return $clean;
	}
	
	
	
	function toAscii1($str) {
		setlocale(LC_ALL, 'en_US.UTF8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '-', $clean);

	return $clean;
	}
	
	
	function toAscii2($str) {
		setlocale(LC_ALL, 'en_US.UTF8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_| -]+/", '-', $clean);

	return $clean;
	}
	
	
	function toAscii3($str, $delimiter='-') {
		setlocale(LC_ALL, 'en_US.UTF8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

	// echo toAscii("A piñata is a paper container filled with candy.", ' ');
	// returns: a pinata is a paper container filled with candy


	function toAscii4($str, $replace=array(), $delimiter='-') {
	
		setlocale(LC_ALL, 'en_US.UTF8');
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}

}