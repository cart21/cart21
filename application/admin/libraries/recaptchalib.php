<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class recaptchalib {
 
    function __construct(){
       
    	$this->CI =& get_instance();
    	
    }
    
    function install($action){
	
    	
    	$modification[]=array(
    			
    			"action"=> $action,
    			"filename"=>'application/front/views/templates/account/login.tpl',
    			"type"=>"after",
    			
    			"find"=>'<input type="password"  name="re-password" class="form-control" placeholder="{$L.repassword}" minlength=5 required />
	                                        </div>
	                                    </div>',
    						
    			"plugin"=>'
    			<!-- recaptchalib plugin -->
	                                    {if $ci->plugin_model->plugin_key_staus("recaptchalib")}
	                                    <div class="form-group">
											<label class="col-sm-4 control-label" >{$L.securitycode}</label>
											<div class="col-md-4">  {$recaptcha}     </div>
	                                    
	                                    </div>
	                                    {/if}
    			<!-- recaptchalib plugin -->
    			'
    	);
    	
    	$modification[]=array(
    			"action"=> $action,
    			"filename"=>'application/front/controllers/account.php',
    			"type"=>"after",
    			
    			"find"=>'$this->form_validation->set_rules("agreement", $this->language_model->language_c_key("registeragrement"), "trim|required|xss_clean");',
    	
    			"plugin"=>'
    			/// recaptcha plugin
        			$this->form_validation->set_rules("recaptcha_response_field", $this->language_model->language_c_key("securitycode"), "trim|required|xss_clean|callback_check_capcha");
        		/// recaptcha plugin
    			'
    	);
    	
    	$modification[]=array(
    			"action"=> $action,
    			"filename"=>'application/front/controllers/account.php',
    			"type"=>"before",
    			 
    			"find"=>'$this->smarty->view("account/login",$this->data);',
    			 
    			"plugin"=>'
    			
    			/// recaptcha plugin
        			$this->load->library("recaptchalib");
    				$this->recaptchalib->recaptcha_get_html();
        		/// recaptcha plugin
    			
    			'
    	);
    	 
    	return $this->CI->modify_file($modification,$action);
    }
    
  
  
    
}

?>