<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 
class CI_image{
 	
	function __construct(){
		 
		$this->CI =& get_instance() ;
	
	
	}
	function dd(){
		//dbg($this->CI->data);
	}
	
	function get_resize_data(){
		
		return $this->CI->db->get("image_resize")->result_array();
	}

	function resize_image($file, $w, $h, $crop=FALSE,$save_as=null) {
	
		//$finfo=pathinfo($file);
	
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
			} else {
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			} else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
	
		$mimetype=mime_content_type($file);
		 
		/*
		 $fi = new finfo(FILEINFO_MIME,$file);
		$mimetype = $fi->buffer(file_get_contents($file));
		*/
		if( $mimetype== 'image/jpeg' ){
			$src = imagecreatefromjpeg($file);
	
	
		}elseif($mimetype== 'image/png' ){
			$src = imagecreatefrompng($file);
		}else{
	
			$src = imagecreatefromjpeg($file);
		}
	
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
		if($save_as){
	
			if( $mimetype== 'image/jpeg' ){
				imagejpeg( $dst, $save_as);
			}elseif($mimetype== 'image/png' ){
				imagepng( $dst, $save_as);
			}
		}
		return $dst;
	}
	
	function create_thumb($file){
		 
		if( ! strpos($_SERVER["DOCUMENT_ROOT"], $file) ){
			$file=$_SERVER["DOCUMENT_ROOT"].$file;
		}
		 
		$file_info=pathinfo($file);
		 
		foreach ($this->get_resize_data() as $k=>$dimension){
			 
			$thumb_dir=str_replace(	$_SERVER["DOCUMENT_ROOT"]."/uploads"	,		$_SERVER["DOCUMENT_ROOT"]."/uploads/".$dimension["folder"]	,	$file_info["dirname"]);
			create_dir(	$thumb_dir);
			$save_as=$thumb_dir."/".$file_info["basename"];
	
	
			$this->resize_image($file,  $dimension["width"], $dimension["height"], $crop=FALSE,$save_as) ;
		}
		 
	}
	
	function delete_img_thumb($file){
	
		if( ! strpos($_SERVER["DOCUMENT_ROOT"], $file) ){
			$file=$_SERVER["DOCUMENT_ROOT"].$file;
		}
		 
		if(file_exists($file)){
	
			unlink($file);
			foreach ($this->get_resize_data() as $k=>$dimension){
		   
				$thumb=str_replace(	$_SERVER["DOCUMENT_ROOT"]."/uploads"	,		$_SERVER["DOCUMENT_ROOT"]."/uploads/".$dimension["folder"]	,	$file);
				if(file_exists($thumb)){
					unlink($thumb);
				}
			}
		}
	}
	
 }
