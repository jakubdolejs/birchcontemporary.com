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

$route['admin/login'] = "admin/login";
$route['admin/logout'] = "admin/logout";
$route['admin/images'] = "image_admin";
$route['admin/image_upload'] = "image_admin/upload";
$route['admin/image/(:num)/delete'] = "image_admin/delete/$1";
$route['admin/image/(:num)'] = "image_admin/edit/$1";
$route['admin/artists'] = "artist_admin";
$route['admin/artist/(:any)/images/(:any)'] = "artist_admin/images/$1/$2";
$route['admin/artist/(:any)/images'] = "artist_admin/images/$1";
$route['admin/artist/(:any)/delete'] = "artist_admin/delete/$1";
$route['admin/artist/(:any)'] = "artist_admin/edit/$1";
$route['admin/exhibitions'] = "exhibition_admin";
$route['admin/exhibitions/(\d{4})'] = "exhibition_admin/index/$1";
$route['admin/exhibition/create'] = "exhibition_admin/add";
$route['admin/exhibition/(:any)/delete'] = "exhibition_admin/delete/$1";
$route['admin/exhibition/(:any)/images'] = "exhibition_admin/images/$1";
$route['admin/exhibition/(:any)'] = "exhibition_admin/edit/$1";
$route['admin/news/create'] = "news_admin/create";
$route['admin/news/(:num)/delete'] = "news_admin/delete/$1";
$route['admin/news/(:num)'] = "news_admin/index/$1";
$route['admin/news'] = "news_admin";
$route['admin/users'] = "user_admin";
$route['admin/user/delete'] = "user_admin/delete";
$route['admin/user/add'] = "user_admin/add";
$route['admin/user/edit'] = "user_admin/edit";
$route['admin/user/password/(:any)'] = "user_admin/password/$1";
$route['admin/user/password'] = "user_admin/password";
$route['admin/tracking'] = "tracking_admin/visitors";
$route['admin/tracking/artists'] = "tracking_admin/artists";
$route['admin/tracking/visitors'] = "tracking_admin/visitors";
$route['admin/tracking/artist/(:any)'] = "tracking_admin/artist/$1";
$route['admin/tracking/visitor'] = "tracking_admin/visitor";
$route['admin/tracking/visit/(:any)'] = "tracking_admin/visit/$1";
$route['api/artists'] = "api/artists";
$route['api/artist/(:any)/delete_cv/(:any)'] = "api/delete_cv/$1/$2";
$route['api/artist/(:any)/images'] = "api/artist_images/$1";
$route['api/exhibition/(:any)/images'] = "api/exhibition_images/$1";
$route['api/images'] = "api/images";
$route['api/vimeo/(:any)'] = "api/vimeo/$1";
$route['admin'] = "admin";
$route['sitemap.xml'] = "sitemap";
$route['download/cv/(:any)'] = "artist/download_cv/$1";
$route['news/subscribe'] = "news/subscribe";
$route['artist/(:any)/exhibition/(:any)/image/(:num)'] = "artist/exhibition_image/$1/$2/$3";
$route['artist/(:any)/image/(:any)'] = "artist/image/$1/$2";
$route['artist/(:any)/exhibitions'] = "artist/exhibitions/$1";
$route['artist/(:any)/exhibition/(:any)'] = "artist/exhibition/$1/$2";
$route['artist/(:any)/cv'] = "artist/cv/$1";
$route['artist/(:any)/news'] = "artist/news/$1";
$route['artist/(:any)/archived'] = "artist/view/$1/1";
$route['artist/(:any)'] = "artist/view/$1";
$route['contact'] = "gallery";
$route['exhibitions'] = "exhibition";
$route['past_exhibitions'] = "exhibition/past";
$route['exhibition/(:any)/image/(:num)'] = "exhibition/image/$1/$2";
$route['exhibition/(:any)'] = "exhibition/view/$1";
$route['artists'] = "artist";
$route['about'] = "about";
$route['news'] = "news";
$route['admin/manifest.appcache'] = "admin/appcache";
$route['images/(:any)/(:num)-(:num).jpg'] = "image/jpeg/$1/$2";
$route['default_controller'] = "artist";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */