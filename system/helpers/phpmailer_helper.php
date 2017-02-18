<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');



function SendMailWithGmailSMTP($to,$subject,$text,$From=""){
	
	
	 $CI =& get_instance();
	 
		require_once("phpmailer/class.phpmailer.php");
		
		$mail=new PHPMailer();
		$mail->IsSMTP();
		
		$mail->SMTPAuth=true;
		
		$mail->Host		=$CI->data["settings"]["smtp_host"];
		$mail->Username	=$CI->data["settings"]["smtp_user"];
		$mail->Password	=$CI->data["settings"]["smtp_pass"];
		
		$mail->FromName	=$CI->data["settings"]["smtp_sendername"];
		
		
		empty($From) ?	$mail->From		=$CI->data["settings"]["smtp_email"]
		: $mail->From	=$From;
		
		
		$mail->Port =$CI->data["settings"]["smtp_port"];
		
		if($mail->Port=="587"){
		
			$mail->SMTPSecure = 'tls';
		}else if($mail->Port=="465"){
			
			$mail->SMTPSecure = 'ssl';  //////////////////dynamic
		}elseif($mail->Port=="25"){
			$mail->SMTPSecure = 'tls';
		}else{
			
		}
		
		$mail->CharSet="utf-8";
		$mail->AddAddress($to);
		$mail->Subject=$subject;
		$mail->IsHTML(true);
		$mail->Body=$text;
		
		if($mail->smtpConnect()){
			if($mail->Send()){
				return true;
			}else{
				echo $mail->ErrorInfo;
				
			}
		}else{
			
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				
			
			mail($to, $mail->Subject.'', $text,$headers);
		}
	 
	}		



