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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'product/browse';

// Product routes (customer)
$route['product/browse'] = 'product/browse';
$route['product/list'] = 'product/list';
$route['cart/add'] = 'product/add_to_cart_api';    // POST API
$route['cart/list'] = 'product/cart_list';        // CMS view
$route['cart/remove'] = 'product/remove_from_cart_api';
// Cart API: update quantity and list items (JSON)
$route['cart/update'] = 'product/update_cart_api'; // POST API
$route['cart/items'] = 'product/cart_items_api';  // GET API

// Product routes (admin)
$route['product'] = 'product/index';
$route['product/add'] = 'product/add';
$route['product/edit/(:num)'] = 'product/edit/$1';
$route['product/update/(:num)'] = 'product/update/$1';
$route['product/delete/(:num)'] = 'product/delete/$1';
$route['product/get_all'] = 'product/get_all';

// Admin routes
$route['admin/login'] = 'admin/login';
$route['admin/logout'] = 'admin/logout';
$route['admin'] = 'product';
$route['admin/create'] = 'admin/create_admin';
// Migration runner (protected by admin session)
$route['admin/migrate'] = 'migrate/run';

// Order routes (customer)
$route['order/checkout'] = 'order/checkout';
$route['order/payment/(:num)'] = 'order/payment/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
