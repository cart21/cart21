<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class CIphpmailer {

var $CI;

	function __construct(){
	
		 $this->CI =& get_instance() ;
		 $this->set_phpmailer();
		
	}
	
	function set_phpmailer(){
	
	$this->default_smtp =$settings=$this->CI->db->query("select * from settings_options where site_url like '%".$_SERVER["SERVER_NAME"]."%'")->row_array();
	
	require_once("phpmailer/class.phpmailer.php");
	$this->phpmailer=new PHPMailer();
	}

	function set_connect($smtp){
	
		$this->phpmailer->IsSMTP();
		$this->phpmailer->SMTPAuth=true;
		
		$this->phpmailer->Host		=$smtp["smtp_host"];
		$this->phpmailer->Username	=$smtp["smtp_user"];
		$this->phpmailer->Password	=$smtp["smtp_pass"];
		$this->phpmailer->FromName	=$smtp["smtp_sendername"];
		
		$this->phpmailer->Port =$this->CI->data["settings"]["smtp_port"];
		
		if($this->phpmailer->Port=="587"){
		
			$this->phpmailer->SMTPSecure = 'tls';
		}else if($this->phpmailer->Port=="465"){
			
			$this->phpmailer->SMTPSecure = 'ssl';  //////////////////dynamic
		}
		
		$this->phpmailer->CharSet="utf-8";
		$this->phpmailer->IsHTML(true);
		
	}

	function SendMailWithSMTP($to,$subject,$text,$From=""){
	
		$this-> set_connect($this->default_smtp);
		empty($From) ?	$this->phpmailer->From	=$this->CI->data["settings"]["smtp_email"]: $this->phpmailer->From=$From;
		
		$this->phpmailer->AddAddress($to);
		$this->phpmailer->Subject=$subject;
		$this->phpmailer->Body=$text;
		
		if($this->phpmailer->Send()){
			return true;
		}else{
			echo false; //$this->phpmailer->ErrorInfo;
		}
			
	}		

}

