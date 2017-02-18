<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
///admin
class quick {

	var $CI;

	function __construct(){
		
		 $this->CI =& get_instance() ;
		 $this->errors="";
		 $this->success="";
		 
	}
	
	function logged_in(){
	
		if ($this->CI->sessiondd->userdata('admin') ){
			return true;
			 }else { 
			 return false;
			 }
		
	}
	
	function onlyLoginUser(){

		  if(!$this->logged_in()) {
		
		redirect('admin/login');
		exit;
		}
		
	}
	
	function get_admin(){

		if($this->logged_in()) {
		
		return $this->CI->sessiondd->userdata('admin');
		}else{
		
		return false;
		}
		
	}
	
	function get_admin2(){

		if($this->logged_in()) {
		
		return (object) $this->CI->sessiondd->userdata('admin');
		}
		
	}
	function language_id(){
		
		return $_SESSION["cart21_a_language"]["language_id"];
	}
	
	function Header() {
	
	$this->data=array();
	 
	$this->data["title"]="Admin ".$this->CI->data["settings"]["site_title"];
	
	$this->CI->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array($this->CI->permission->permission_section))->result_array(),"text_val","key_val");
	
   	$this->CI->data["Header"]=$this->CI->smarty->fetch('header.tpl',$this->data);
   	
	return $this->CI->data["Header"];
	
	}
	
	function Footer($smarty) {
	
		$this->CI->data["Footer"]=$this->CI->smarty->fetch('footer.tpl');
		
	return  $this->CI->data["Footer"];
	
	}
	
	function Top_menu($smarty) {

	$this->data=array();
	$this->data["ci"] =$this->CI;
	$this->data["errors"]=$this->errors;
	$this->data["success"]=$this->success;
	
	$this->data["settings"] =$this->CI->data["settings"];
	
	$this->data["actif_class_menu"] =$this->CI->uri->segment(2);
	
	$this->data["logs"] = $this->CI->db->query("SELECT * FROM `admin_logs`  
	 		 
	 		where  FROM_UNIXTIME(`date_added`,'%Y-%m-%d') = curdate()  order by logs_id desc");
	
	$this->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array("topmenu"))->result_array(),"text_val","key_val");
	
	$this->data["languages"]=$this->CI->language_model->languages();
	
	$this->data["plugins_menu"] =$this->CI->db->query("SELECT * FROM `plugin`  WHERE `status`=1 and `manage_link` is not null")->result_array();
	
	$this->data["meta_types"]=$this->CI->meta_model->meta_types()->result_array();
	
	 
	$this->CI->data["Topmenu"] =$this->CI->smarty->fetch('top_menu.tpl',$this->data);
	
	return  $this->CI->data["Topmenu"];
	
	}
	
	function get_query_array(){
				
		$url=$this->CI->input->server('QUERY_STRING');
		if(!empty($url)){
		
			if(strpos($url,"?")){
				$url=substr(strstr($url,"?"),1);
			}
			//$this->dbg2($url);
			$url=strpos($url,"&")?explode("&",$url): array($url);
			
			
			if($url){
			
				foreach($url as $get){
			
					$result=explode("=",$get);

				$results[$result[0]]=$result[1];
				}
			}else {return false;}
			
			//$this->dbg2($results);
		return $results;
	}else{
	return false;
	}
	}
	
	function get_uri_segment(){
	////
		$uri=$this->CI->input->server('REQUEST_URI');
		$uri=array_slice(explode('/',$uri),1);
		//$this->dbg2( $uri);
		return $uri;
	  ////
	}
	
	function create_uri_link($results){

		$link="";
		foreach($results as $key=>$value){

		$results[$key]=$key."=".$value;
		}

	return implode("&",$results);
	}
	
	function get_date_data($begin_date,$end_date){
   
		
		$day_diff=($end_date - $begin_date)/86400 ;
	
		$day= $begin_date;
		for($i=1; $i<=$day_diff;$i++){
		
			$result[$day]= date('m/d/Y',$day);
			$day +=86400;
		}
		
		return ($result);
   }
   
    function get_date_data2($begin_date,$end_date){
   
		
		$day_diff=($end_date - $begin_date)/86400 ;
	
		$day= $begin_date;
		for($i=1; $i<=$day_diff;$i++){
		
			$result[$i]= $day;
			$day +=86400;
		}
		
		return ($result);
   }
   function set_date2($time){
   
   	$result=getdate($time);
   
   
   	return $result ;
   }
	
	function upload($name){

		$result=array();
		$files = $_FILES;
		
		$cpt = count($_FILES[$name]['name']);
		for($i=0; $i<$cpt; $i++){
	
			$_FILES[$name]['name']= $files[$name]['name'][$i];
			$_FILES[$name]['type']= $files[$name]['type'][$i];
			$_FILES[$name]['tmp_name']= $files[$name]['tmp_name'][$i];
			$_FILES[$name]['error']= $files[$name]['error'][$i];
			$_FILES[$name]['size']= $files[$name]['size'][$i]; 
			
		if ( ! $this->CI->upload->do_upload($name)){
			$error = $this->CI->upload->display_errors();
			$result["error"][]=$error;
		}else{
			$file_data =  $this->CI->upload->data();
			$result["success"][]=$file_data;
		}
		
		}
		
		 return  ($result);
	
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


# PHP < 5.5
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

	function email_exist($email){
    	
    	$this->CI->db->where("email",$email);
    	$customer=$this->CI->db->get("customer");
    	//$this->quick->dbg2($customer->row());
    	
    	if($customer->num_rows>0){
    	return true;
    	}else{
    	 
    	return false;
    	}
    
    }
    


	
}