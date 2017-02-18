<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class latest_products_v2 {
 
    function __construct(){
       
    	$this->CI =& get_instance();
    	
    }
    
    function install($action){
	
   
    	$modification[]=array(
    			
    			"action"=> $action,
    			"filename"=>'application/front/models/product_model.php',
    			"type"=>"before",
    			"find"=>'function product_related(',
    						
    			"plugin"=>'
    			/// plugin latest product on the left ///
				
				function latest_product($limit,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=f_language_id();
    	}
    	 
    	
    	$sql="select *,if(p.discount_type=1, p.price-p.discount, (p.price-(p.discount*p.price)/100)  ) as price_d from product as p
    			
    			left join (select image_loc,product_id from product_image where default_img=1 ) as pi
    			
    			on pi.product_id=p.pl_id
    			
    			where p.language_id=".$language_id."  and p.status=1 order by p.pl_id desc limit ".$limit;
    	 
    	return $this->db->query($sql);
    }
				
				/// plugin latest product on the left ///
    			'
    	);
    	
    	
    	
    	
    	
    	return $this->CI->modify_file($modification,$action);
    }
    
  
  
    
}

?>