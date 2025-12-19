<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'frontend/survei_pm';
$route['404_override'] = 'notfound_404';
$route['translate_uri_dashes'] = FALSE;
$route['submit'] = 'login/actiondata';
$route['home'] = 'frontend';
$route['privacy-policy'] = 'login/term';
$route['register'] = 'frontend/register';
$route['forgot'] = 'login/forgetpassword';
$route['reset/(:any)'] = 'login/resetpassword/$1';
$route['kontak'] = 'frontend/contact';
$route['faq'] = 'frontend/faq';
$route['search'] = 'frontend/search';
$route['search/(:num)'] = 'frontend/search';
$route['logout'] = 'login/logout';
$route['dokumenview/(:any)'] = 'data_gallery/viewdata/$1';
$route['dokumen/(:any)'] = 'frontend/viewdata/$1';
$route['request_csrf_token'] = 'login/refresh_csrf_token';
$route['remove_chat'] = 'login/delete_old_chat';
$route['sitemap.xml'] = 'login/sitemap';
$route['manifest.json'] = 'login/manifest';
$route['survei'] = 'frontend/survei_pm';

require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();
$query = $db->get( 'data_page' );
$result = $query->result();
if($result){
	foreach( $result as $row ){
	   $route[ $row->slug ] = 'frontend/page';
	}
}

$db->select('data_article.slug, master_article_jenis.name');
$db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','inner');
$query = $db->get( 'data_article' );
$result = $query->result();
if($result){
	foreach( $result as $row ){
		$route[ strtolower($row->name).'/'.$row->slug ] = 'frontend/post';
	}
}



