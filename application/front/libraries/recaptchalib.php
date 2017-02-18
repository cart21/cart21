<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once(APPPATH.'libraries/recaptcha/recaptchalib.php');
 
class recaptchalib {
 
    function __construct(){
    
    $this->CI =& get_instance() ;
    $this->get_param();
	   $this->error = "";

    
    }
    
    function get_param(){
    		
    	$this->param=unserialize($this->CI->db->where("p_key","recaptchalib")->get("plugin")->row()->param);
    	
    	$this->publickey =	$this->param["recaptcha"]["publickey"];  //"6Ld9G-wSAAAAAFnqhl0dOjLp20wNq9RhlYLmxUo-";
    	$this->privatekey =	$this->param["recaptcha"]["privatekey"]; //"6Ld9G-wSAAAAAAFuj_6_BMwfd840NMALeu_rAbqV";
    	$this->ssl =	$this->param["recaptcha"]["ssl"];
    }
    
    function recaptcha_get_html(){
    
    	$this->CI->data["recaptcha"]=recaptcha_get_html($this->publickey, $this->error,$this->ssl);
     return $this->CI->data["recaptcha"];
    
    }
   function check_capcha($field){
   
   if ($field) {
        $resp = recaptcha_check_answer ($this->privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $this->CI->input->post("recaptcha_challenge_field"),
                                        $field);

        if ($resp->is_valid) {
               return true; //echo "You got it!";
        } else {
                # set the error code so that we can display it
                $this->error = $resp->error;
                return false;
        }
}


   }
   
  
}

?>