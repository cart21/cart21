<?php
class offer extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    
    function index() {
    	$this->permission->check_permission("view");
    
    	
    	$this->quick->Header("");
    	$this->quick->Top_menu("");
    	$this->quick->Footer("");
    	
    $this->smarty->view('management/offer',$this->data);
    }
    
    
    function dd(){
    	$count = 0;
    	$filename = 'application/front/views/templates/account/login.tpl';
    	$filename1 = 'application/admin/controllers/product.php';
    	$find='function default_image(){
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
	
		$this->db->query("update product_image set default_img=0 where product_id=".$this->input->post("product_id"));
		$this->db->query("update product_image set default_img=1 where product_image_id=".$this->input->post("image_id"));
	
		echo 1;
		exit;
	
	}';
    	$content=file_get_contents($filename1);
    	
    	//dbg( $content); exit;
 	   $new_content=preg_replace('/'.preg_quote($find).'/' , $find."
 			
 			ddd fdd(){ }", $content) ;
    	echo $new_content;
    	//file_put_contents("sitevisitors.txt", $new_content );
    	echo $count;
    	exit;
    	
    	if (file_exists($filename))
    	{
    		$count = file($filename);
    		
    		foreach ($count as $c){
    			
    			if(trim($c)==trim($find)){
    				echo '<b>'.htmlspecialchars($c).'</b><br>';
    			
    			}else{
    			echo htmlspecialchars($c).'<br>';
    			}
    		}
    		/*
    		$fp = fopen("sitevisitors.txt", "w");
    		fputs ($fp, "$c");
    		fclose ($fp);
    		*/
    		//$count[0] ++;
    		
    		//echo $count[0];
    	}
    	/*
    	else
    	{
    		$fh = fopen("sitevisitors.txt", "w");
    		if($fh==false)
    			die("unable to create file");
    		fputs ($fh, 1);
    		fclose ($fh);
    		$count = file('sitevisitors.txt');
    		echo $count[0];
    	}
    	 */
    	
    }
    
     
		
}