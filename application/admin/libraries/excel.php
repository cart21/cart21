<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once(APPPATH.'libraries/objPHPExcel/Classes/PHPExcel.php');
 
class excel {
 
    function __construct(){
       
        $this->CI =& get_instance() ;
        
         $this->CI->objPHPExcel= new PHPExcel();
    }
    
}