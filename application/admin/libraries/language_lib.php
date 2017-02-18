<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
require_once(APPPATH.'libraries/objPHPExcel/Classes/PHPExcel.php');
 
class language_lib {
 
    function __construct(){
       
        $this->CI =& get_instance() ;
        
        
    }
    
   function download($data_to_excel){
   	
   	// Set document properties
   	$this->CI->objPHPExcel->getProperties()->setCreator("Muslum CEN")
   	->setLastModifiedBy("Muslum CEN")
   	->setTitle("Cart21 Language ")
   	->setSubject("Cart21 Language " )
   	->setDescription("Cart21 Language " )
   	->setKeywords("cart21 shopping cart ")
   	->setCategory("Language");
   	
   	
   	$this->CI->objPHPExcel->setActiveSheetIndex(0)
   	->setCellValue('A1',"key_val" )
   	->setCellValue('B1', "text_val")
   	->setCellValue('C1', "Page Assign Number")
   	->setCellValue('D1', "Section Assign Number");
   	
   	$i=2;
   	// Add some data
   	foreach ($data_to_excel as $l){
   		$this->CI->objPHPExcel->setActiveSheetIndex(0)
   		->setCellValue('A'.$i, $l["key_val"])
   		->setCellValue('B'.$i, $l["text_val"]);
   	
   		$lucy_page=$this->CI->language_model->language_to_page($l["language_c_id"]);
   	
   		if($lucy_page->num_rows){
   			$this->CI->objPHPExcel->setActiveSheetIndex(0) ->setCellValue('C'.$i, implode(",",array_column($lucy_page->result_array(),"meta_id")) );
   		}
   	
   		$lucy_section=$this->CI->language_model->language_to_section($l["language_c_id"]);
   		if($lucy_section->num_rows){
   				
   			$this->CI->objPHPExcel->setActiveSheetIndex(0) ->setCellValue('D'.$i, implode(",",array_column($lucy_section->result_array(),"section_id")) );
   		}
   	
   		$i++;
   	}
   	// Miscellaneous glyphs, UTF-8
   	
   	foreach(range('A','F') as $columnID) {
   		$this->CI->objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
   		->setWidth(30);
   	}
   	
   	// Rename worksheet
   	$this->CI->objPHPExcel->getActiveSheet()->setTitle('Language file');
   	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
   	$this->CI->objPHPExcel->setActiveSheetIndex(0);
   	// Redirect output to a client’s web browser (Excel5)
   	header('Content-Type: application/excel');
   	header('Content-Disposition: attachment;filename="language.xls"');
   	header('Cache-Control: max-age=0');
   	// If you're serving to IE 9, then the following may be needed
   	header('Cache-Control: max-age=1');
   	// If you're serving to IE over SSL, then the following may be needed
   	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
   	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
   	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
   	header ('Pragma: public'); // HTTP/1.0
   	$objWriter = PHPExcel_IOFactory::createWriter($this->CI->objPHPExcel, 'Excel5');
   	$objWriter->save('php://output');
   	exit;
   }
   
   
}