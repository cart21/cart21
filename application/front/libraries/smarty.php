<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once(APPPATH.'libraries/smarty/libs/Smarty.class.php');
 
class CI_Smarty extends Smarty {
	var $CI;
	
	function __construct(){
		$this->CI =& get_instance() ;
		
    	$settings_option=$this->CI->db->query("SELECT * FROM `settings_options`")->row();
    	
      
        parent::__construct();
        
        $this->setTemplateDir(APPPATH.'views/templates/'.$settings_option->front_theme);
        $this->setCompileDir(APPPATH.'views/compiled');
        $this->setConfigDir(APPPATH.'libraries/smarty/configs');
        $this->setCacheDir(APPPATH.'libraries/smarty/cache');
 
        $this->assign( 'APPPATH', APPPATH );
        $this->assign( 'BASEPATH', BASEPATH );
        // $this->caching = Smarty::CACHING_LIFETIME_CURRENT; // Does something <img src="http://searchdaily.net/wp-includes/images/smilies/icon_smile.gif" alt="icon smile CodeIgniter 2 Smarty 3 integration" class="wp-smiley" title="CodeIgniter 2 Smarty 3 integration"> 
        if ( method_exists( $this, 'assignByRef') )
        {
        	
            $this->assignByRef("ci", $this->CI);
        }
        
        $this->force_compile = 1;
        $this->caching = false;
        $this->cache_lifetime = 12;
       
        //log_message('debug', "Smarty Class Initialized");
    }
 
    
    function view($template_name,$data = array() ) {
    	$data["ci"]=& get_instance();
    	foreach ($data as $key => $val)
		{
			$this->assign($key, $val);
		}
    
        if (strpos($template_name, '.') === FALSE && strpos($template_name, ':') === FALSE) {
            $template_name .= '.tpl';
        }
        parent::display($template_name);
    }
    
    function fetchdd($template_name,$data = array() ) {
    
    	foreach ($data as $key => $val)
		{
			$this->assign($key, $val);
		}
    
        if (strpos($template_name, '.') === FALSE && strpos($template_name, ':') === FALSE) {
            $template_name .= '.tpl';
        }
        parent::fetch($template_name);
    }
 
}
 
?>