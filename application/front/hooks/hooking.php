<?php



class hooking {
   
    function __construct(){
   include(APPPATH."config/database.php");


	$this->link=mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password'],$db['default']['database']) or die('Server connexion not possible.');
	//mysqli_select_db($db['default']['database'])  or die('Database connexion not possible.');
 
    if ($this->link->connect_errno) {
    printf("Connect failed: %s\n", $this->link->connect_error);
    exit();
	}
	if (!$this->link->set_charset("utf8")) {
		printf("Error loading character set utf8: %s\n",$this->link->error);
		exit();
	} else {
		//printf("Current character set: %s\n",$this->link->character_set_name());
	}

    }  
    
    function specialquery($sql) {

    	$resource=mysqli_query($this->link,$sql);
        
        
        $i = 0;
    	$data = array();
    	if ($resource->num_rows) {
    		
    			while ($result = mysqli_fetch_array($resource,MYSQLI_ASSOC) ) {
    				$data[$i] = $result;
    
    				$i++;
    			}
    
    			mysqli_free_result($resource);
    
    			$query = new stdClass();
    			$query->num_rows = $i;
    			$query->row = isset($data[0]) ? $data[0] : array();
    			$query->rows = $data;
    			
    			unset($data);

    			
    		
    	} else {
    	
    			$query = new stdClass();
    			$query->num_rows = $i;
    			$query->row = isset($data[0]) ? $data[0] : array();
    			$query->rows = $data;
    	}
	return $query;
    }
    
    //$Functions->activeEdit('alt_products', $product, array('ID' => $ProductID ) );	 /// only ID
	function activeEdit($table , $datas ,$where  ) {
	
	$sql=" UPDATE ".$table." SET ";
	foreach($datas as $key => $value){
	
		$sql2[]=$key."='".$value."'";	
	
	}
	
	$sql.=implode(' , ',$sql2);
	
	foreach($where as $key => $value){
	
		$sql3[]=$key." IN ( ".$value;	
	}
	
	$sql.=" WHERE ".implode(' AND  ',$sql3)." )";
	//echo $sql;
	$this->db->query($sql);
	}	
	
	//$Functions->activeEditAND('alt_products', $product, array('ID' => $ProductID ) );
	function activeEditAND($table , $datas ,$where  ) {
	
	$sql=" UPDATE ".$table." SET ";
	foreach($datas as $key => $value){
	
		$sql2[]=$key."='".$value."'";	
	
	}
	
	$sql.=implode(' , ',$sql2);
	
	foreach($where as $key => $value){
	
		$sql3[]=$key." = '".$value."'";	
	}
	
	$sql.=" WHERE ".implode(' AND  ',$sql3)." ";
	//echo $sql;
	$this->db->query($sql);
	}	
	
	//$Functions->activeInsert('tablenam',array('column'=>'dd'));
	function activeInsert($table , $datas  ) {
	
	$sql=" INSERT INTO ".$table." SET ";
	foreach($datas as $key => $value){
	
		$sql2[]=$key."='".$value."'";	
	
	}
	
	$sql.=implode(' , ',$sql2);
	
	//echo $sql;
	$this->db->query($sql);
	
	}	
	
	function existInTable($table, $where  ){
		
		return $this->getTable($table,$where )->num_rows;
	}
	
	//$Naturel->dbg2( $Functions->getTable("alt_basket", array("ProductID"=>148) ) );
	function getTable($table,$where=array('1'=>'1') ) {
	
		$sql=" SELECT * FROM  ".$table."  ";
		
		
		foreach($where as $key => $value){
		
			$sql3[]=$key."='".$value."'";	
		}
		
		$sql.=" WHERE ".implode(' AND  ',$sql3)." ";

	return $this->specialquery($sql);
	}	

	function getTableIN($table,$where,$id ) {
	
		$sql=" SELECT * FROM  ".$table."  ";
		
		$sql.=" WHERE ".$id." in(".$where.") ";
		
		return $this->specialquery($sql);
	}
	function GroupConcat($table,$col,$where) {
	
		$sql="SELECT  group_concat(".$col.") as ".$col." FROM ".$table." ";
		
		foreach($where as $key => $value){
		
			$sql3[]=$key." in(".$value.")";	
		}
		
		$sql.=" WHERE ".implode(' AND  ',$sql3)." ";
	return $this->specialquery($sql);
	}
     
  
    
    
    
   	 function dbg($array){
		echo '<pre>'; var_dump($array); echo '</pre>';
	}
	function dbg2($array){
		echo '<pre>'; print_r($array); echo '</pre>';
	}


	function close_db() {	    
	    
	 mysqli_close($this->link);
	 }
    
}