<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

/*
$route['default_controller'] = "welcome";
$route['404_override'] = '';
*/


$route['pages/(:any)'] = 'pages/view/$1';
/*
 $route['content.html/(:num)'] = 'content/category/$1';
$route['content.html/(:num)/(:num)'] = 'content/category/$1/$2';
*/
require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();
///
$uri=substr($_SERVER["REQUEST_URI"],1);



if(strstr($uri,"?",true)){
	$uri1=strstr($uri,"?",true);
	$uri2=strstr($uri,"?");
}else{
	$uri1= $uri;
	$uri2="";
}

$uri1=rtrim($uri1,"/");

$db_routes=$db->limit(1)->get_where("meta",array("link"=>mysql_real_escape_string($uri1)));
if($db_routes->num_rows()>0){
	$route[$uri1] = $uri2 ? $db_routes->row()->class_routes."/".$uri2 : $db_routes->row()->class_routes;

}

/*
 echo '<pre>';var_dump($route);echo '</pre>';
echo $uri1."<br>";
echo $uri2."<br>";
*/

///
$default_controller=$db->limit(1)->get_where("meta",array("link"=>"home"));

if($default_controller->num_rows>0){
	$route['default_controller']=$default_controller->row()->class_routes;
}else{
	$route['default_controller'] = 'index';
}
///

///
$routes=$db->get_where("meta",array("type"=>"9"));
if($routes->num_rows>0){
	foreach($routes->result_array() as $rut){
		$route[$rut["link"]]=$rut["class_routes"];
	}
}
///

$route['404_override'] = 'index/index';


/* End of file routes.php */
/* Location: ./application/config/routes.php */