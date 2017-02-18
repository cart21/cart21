<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


	function dbg2($array){
		echo '<pre>'; var_dump($array); echo '</pre>';
	}
	
	function dbg($array){
		echo '<pre>'; print_r($array); echo '</pre>';
	}	
	
	 # PHP < 5.5
if(!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
    
        if (null === $indexKey) {
            if (null === $columnKey) {
                // trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
                $result = array_values($input);
            }
            else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        }
        else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            }
            else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
    
        return $result;
    }
}
    
    if(!function_exists('mime_content_type')) {
    
    	function mime_content_type($filename) {
    
    		$mime_types = array(
    
    				'txt' => 'text/plain',
    				'htm' => 'text/html',
    				'html' => 'text/html',
    				'php' => 'text/html',
    				'css' => 'text/css',
    				'js' => 'application/javascript',
    				'json' => 'application/json',
    				'xml' => 'application/xml',
    				'swf' => 'application/x-shockwave-flash',
    				'flv' => 'video/x-flv',
    
    				// images
    				'png' => 'image/png',
    				'jpe' => 'image/jpeg',
    				'jpeg' => 'image/jpeg',
    				'jpg' => 'image/jpeg',
    				'gif' => 'image/gif',
    				'bmp' => 'image/bmp',
    				'ico' => 'image/vnd.microsoft.icon',
    				'tiff' => 'image/tiff',
    				'tif' => 'image/tiff',
    				'svg' => 'image/svg+xml',
    				'svgz' => 'image/svg+xml',
    
    				// archives
    				'zip' => 'application/zip',
    				'rar' => 'application/x-rar-compressed',
    				'exe' => 'application/x-msdownload',
    				'msi' => 'application/x-msdownload',
    				'cab' => 'application/vnd.ms-cab-compressed',
    
    				// audio/video
    				'mp3' => 'audio/mpeg',
    				'qt' => 'video/quicktime',
    				'mov' => 'video/quicktime',
    
    				// adobe
    				'pdf' => 'application/pdf',
    				'psd' => 'image/vnd.adobe.photoshop',
    				'ai' => 'application/postscript',
    				'eps' => 'application/postscript',
    				'ps' => 'application/postscript',
    
    				// ms office
    				'doc' => 'application/msword',
    				'rtf' => 'application/rtf',
    				'xls' => 'application/vnd.ms-excel',
    				'ppt' => 'application/vnd.ms-powerpoint',
    
    				// open office
    				'odt' => 'application/vnd.oasis.opendocument.text',
    				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    		);
    
    		$ext = strtolower(array_pop(explode('.',$filename)));
    		if (array_key_exists($ext, $mime_types)) {
    			return $mime_types[$ext];
    		}
    		elseif (function_exists('finfo_open')) {
    			$finfo = finfo_open(FILEINFO_MIME);
    			$mimetype = finfo_file($finfo, $filename);
    			finfo_close($finfo);
    			return $mimetype;
    		}
    		else {
    			return 'application/octet-stream';
    		}
    	}
    }
    
    function array_2d_serach(array $input, $indexKey,$search) {
    	$result = array();
    
    	foreach ($input as $row) {
    		if(array_search($search,$row)==$indexKey){
    			$result[] = $row;
    		}
    		
    	}
    
    	return $result;
    }
    
	function language_id(){
		
		return isset($_SESSION["cart21_a_language"]["language_id"]) ? $_SESSION["cart21_a_language"]["language_id"] : null;
	}
	
	function f_language_id(){
	
		return $_SESSION["cart21_language"]["language_id"];
	}
	

	
	function create_dir($dirPath ){
	
		$dirPath=str_replace($_SERVER["DOCUMENT_ROOT"],"",$dirPath); // remove DOCUMENT_ROOT
		$data=explode("/",$dirPath);
	
		$web_root=$_SERVER["DOCUMENT_ROOT"];
	
		foreach (array_filter($data) as $f){
				
			$web_root.="/".$f;
			if (!file_exists($web_root)) {
				mkdir($web_root, 0755);
			}
		}
	
	}
	
	function nextprev_val($arr,$value,$direction="next"){
	
		if($direction=="next"){
			$next_key=array_search($value,$arr)+1;
		}else{
			$next_key=array_search($value,$arr)-1;
		}
	
	
		if(array_key_exists($next_key,$arr)){
			return $arr[$next_key];
		}else{
	
			if($direction=="next"){
				return reset($arr);
			}else{
				return end($arr);
			}
	
		}
	
	}

	
	function toAscii($str, $replace=array(), $delimiter='-') {
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



