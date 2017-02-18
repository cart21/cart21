<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class quick {

	var $CI;

	function __construct(){
		
		 $this->CI =& get_instance() ;
		 $this->errors="";
		 $this->success="";
		 
	}

	function logged_in(){

		$customer=$this->CI->sessiondd->userdata('customer');
		if (isset($customer["customer_id"]) ){
			return true;
			 }else {
			 return false;
			 }
		
	}
	
	function logged_customer(){

		if ($this->CI->sessiondd->userdata('customer') ){
			$customer_array=$this->CI->sessiondd->userdata('customer');
			$this->CI->db->where("customer_id",$customer_array["customer_id"]);
    			$customer=$this->CI->db->get("customer");
			return $customer->row();
		 }else {
			 return false;
		 }
		
	}
	
	function logged_user(){

		if ($this->CI->sessiondd->userdata('customer') ){
			$customer_array=$this->CI->sessiondd->userdata('customer');
			
			return (object)$customer_array;
		 }else {
			 return false;
		 }
		
	}
	function language_id(){
	
		return $_SESSION["cart21_language"]["language_id"];
	}
	
	function onlyLoginUser(){

		  if(!$this->logged_in()) {
		
		redirect($this->CI->quick_model->get_link("/account/login"));
		exit;
		}
		
	}
	
	function load_plugin($page_id){
		
		$this->CI->plugin=$this->CI->plugin_model->page_plugin($page_id);
	
		foreach ($this->CI->plugin->result_array() as $p){
			
			$this->CI->load->library("{$p["p_key"]}");
			$data["positions"][$p["position"]][]=$this->CI->{$p["p_key"]}->index($p);
		}
		
		if(isset($data["positions"]["left"]) and isset($data["positions"]["right"]) ){
			
			$data["col_center"]=6;
		}elseif(isset($data["positions"]["left"]) or isset($data["positions"]["right"])){
			$data["col_center"]=9;
		}else{
			$data["col_center"]=12;
		}
				
		return $data;
	}

	function Header($smarty,$meta_data="") {
		
		/// Meta ///
		$this->data["settings"]=$this->CI->data["settings"];
		$this->data["uri_link"]=isset($this->CI->uri->segments[1]) ? $this->CI->uri->segments[1] : "";
		
		if($this->data["uri_link"]==""){
			$m=$this->CI->quick_model->meta(array("class_routes"=>"index"));
		}else{
			$m=$this->CI->quick_model->meta_non_lang(array("link"=>$this->data["uri_link"] ));
		
			if(!$m->num_rows){
				$m=$this->CI->quick_model->meta(array("class_routes"=>$this->data["uri_link"] ));
			}
		}
		
		if($m->num_rows){
			$meta=$m->row_array();
			
		}elseif(isset($this->CI->data["meta"]) or !empty($this->CI->data["meta"])){
			
			$meta=$this->CI->data["meta"];
			
	 	}else{
	 		$meta["title"]="enter meta data";
			$meta["keywords"]="";
			$meta["description"]="";
			$meta["type"]=0;
	 	}
	 	
	 	$this->data["title"]=$meta["title"];
	 	$this->data["keywords"]=preg_replace( "/\r|\n/", " ",$meta["keywords"]);
	 	$this->data["description"]=preg_replace( "/\r|\n/", " ",$meta["description"]);
	 	
	 	/// Meta ///
	 	
	 	/// constant page ///
	 	if($meta["type"]==1){
	 		
	 		$constant_m_id=$meta["m_id"];
	 	}elseif ($meta["type"]==4 ){
	 	
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"product_page"))->row()->m_id;//9235;
	 	}elseif ($meta["type"]==5 or $this->CI->uri->segments[1]=="category"){
	 	
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"category"))->row()->m_id;
	 	}elseif ($meta["type"]==6 ){
	 	
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"content_view"))->row()->m_id;
	 	}elseif ( $meta["type"]==7 or $meta["type"]==8){
	 	
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"/content/category"))->row()->m_id;
	 	}
	 	elseif ( $meta["type"]==11){
	 		 
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"brand_view"))->row()->m_id;
	 	}elseif ( $meta["type"]==10){
	 	
	 		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"property_view"))->row()->m_id;
	 	}
	 	/// constant page ///
	 	
	 	/// load language
	 	$this->data["L"]=$this->CI->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array($constant_m_id))->result_array(),"text_val","key_val");
	 	 
	 	/// load plugins
	 	$this->CI->data=array_merge($this->load_plugin($constant_m_id),$this->CI->data);
	 
   		$this->CI->data["Header"]=$this->CI->smarty->fetch('header.tpl',$this->data);
   		
	return  $this->CI->data["Header"];
	
	}
	
	function Top_menu($smarty) {
		
		$this->data["errors"]=$this->errors;
		$this->data["success"]=$this->success;
	
		$this->data["settings"]=$this->CI->data["settings"];
		
   		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"top_language"))->row()->m_id;
   		
   		/// load language
   		//$this->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array($constant_m_id))->result_array(),"text_val","key_val");
   	
   		/// load plugins
   		$top_positions=$this->load_plugin($constant_m_id);
   		$this->data["positions"]=isset($top_positions["positions"]) ? $top_positions["positions"] : "";//array_merge(,$this->data);
   		///
   		
   		$this->CI->data["Topmenu"]= $this->CI->smarty->fetch('top.tpl',$this->data);
   	
	return $this->CI->data["Topmenu"];
	
	}
	
	function Footer($smarty) {
		
		$this->data["links"]=$this->CI->quick_model->footer_link();

		$constant_m_id=$this->CI->quick_model->meta(array("class_routes"=>"footer"))->row()->m_id;
		
		/// load language
		//$this->data["L"]=array_column($this->CI->language_model->languga_c_by_page(array($constant_m_id))->result_array(),"text_val","key_val");
		 
		/// load plugins
		$foot_positions=$this->load_plugin($constant_m_id);//array_merge($this->load_plugin($constant_m_id),$this->data);
		$this->data["positions"]=isset($foot_positions["positions"]) ? $foot_positions["positions"] : "";
		
	$this->CI->data["Footer"]= $this->CI->smarty->fetch('footer.tpl',$this->data);
	return  $this->CI->data["Footer"];
	
	}
	
	function get_query_array(){

		$url=$this->CI->input->server('QUERY_STRING');
		if(!empty($url)){
		
			if(strpos($url,"?")){
				$url=substr(strstr($url,"?"),1);
			}
			
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
	
	
	///// Functions ////
	
	function email_exist($email){
    	
    	$this->CI->db->where("email",$email);
    	$customer=$this->CI->db->get("customer");
    	
    	if($customer->num_rows>0){
    	return true;
    	}else{
    	 
    	return false;
    	}
    
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

	function set_date2($time){
	
		$result=getdate($time);
		
		
	return $result ;
	}
	
	function month_day_number($month,$year) {
	
			$leap=$this->is_leap_year($year);
			switch($month)
			{
				case 1:
					$day_numbers=31; // January has 31 days.
					break;
				case 2:
					if($leap)
						$day_numbers=29; // Febrary in leap year. 
					else
						$day_numbers=28; // Febrary in ordinary year.
					break;
				case 3:
					$day_numbers=31;
					break;
				case 4:
					$day_numbers=30;
					break;
				case 5:
					$day_numbers=31;
					break;
				case 6:
					$day_numbers=30;
					break;
				case 7:
					$day_numbers=31;
					break;
				case 8:
					$day_numbers=31;
					break;
				case 9:
					$day_numbers=30;
					break;
				case 10:
					$day_numbers=31;
					break;
				case 11:
					$day_numbers=30;
					break;
				case 12:
					$day_numbers=31;
					break;
			}
	
			return($day_numbers);
		}
 
 	function is_leap_year($y){
 	
			if($y%4==0)
				return(true);
			else
				return(false);
	}

		
	///// Functions ////	


}