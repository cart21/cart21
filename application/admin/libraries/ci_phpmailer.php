<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


class ci_phpmailer {

var $CI;

	function __construct(){
	
		 $this->CI =& get_instance() ;
		 $this->otel_id=0;
		
		 
		require_once("phpmailer/class.phpmailer.php");
	$this->phpmailer=new PHPMailer();
	}
	
	function set_phpmailer(){
	
		if($this->otel_id==0){
		$this->default_smtp =$settings=$this->CI->db->query("select * from settings_options where site_url like '%".$_SERVER["SERVER_NAME"]."%'")->row_array();
		}else{
		$this->default_smtp =$settings=$this->CI->db->query("select * from settings_options where otel_id=".$this->otel_id."")->row_array();
		}
		
		
		$this->phpmailer->IsSMTP();
		$this->phpmailer->SMTPAuth=true;
		
		$this->phpmailer->Host		=$this->default_smtp["smtp_host"];
		$this->phpmailer->Username	=$this->default_smtp["smtp_user"];
		$this->phpmailer->Password	=$this->default_smtp["smtp_pass"];
		$this->phpmailer->FromName	=$this->default_smtp["smtp_sendername"];
		
		$this->phpmailer->Port =$this->CI->data["settings"]["smtp_port"];
		
		if($this->phpmailer->Port=="587"){
		
			$this->phpmailer->SMTPSecure = 'tls';
		}else if($this->phpmailer->Port=="465"){
			
			$this->phpmailer->SMTPSecure = 'ssl';  //////////////////dynamic
		}else if($this->phpmailer->Port=="25"){
			
			$this->phpmailer->SMTPSecure = 'tls';
		}else{
			
		}
		
		$this->phpmailer->CharSet="utf-8";
		$this->phpmailer->IsHTML(true);
		
		return $this->phpmailer->smtpConnect();
	}

	function SendMailWithSMTP($to,$subject,$text,$From=""){
	
		$this->set_phpmailer();
		
		empty($From) ?	$this->phpmailer->From	=$this->CI->data["settings"]["email"]: $this->phpmailer->From=$From;
		
		$this->phpmailer->AddAddress($to);
		$this->phpmailer->Subject=$subject;
		$this->phpmailer->Body=$text;
		
		if($this->phpmailer->Send()){
			return true;
		}else{
			echo $this->phpmailer->ErrorInfo;
		}
			
	}		

}

