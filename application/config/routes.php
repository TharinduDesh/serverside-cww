<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
$route['register'] = 'auth/register';
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['dashboard'] = 'auth/dashboard';
$route['verify-email'] = 'auth/verify';
$route['forgot-password'] = 'auth/forgot_password';
$route['reset-password'] = 'auth/reset_password';

/*
|--------------------------------------------------------------------------
| Profile Management
|--------------------------------------------------------------------------
*/
$route['profile'] = 'profile/index';
$route['profile/save'] = 'profile/save';
$route['profile/delete-image'] = 'profile/delete_profile_image';

/*
|--------------------------------------------------------------------------
| Bidding
|--------------------------------------------------------------------------
*/
$route['bidding'] = 'bidding/index';
$route['bidding/place'] = 'bidding/place_bid';
$route['bidding/update/(:num)'] = 'bidding/update_bid/$1';
$route['bidding/run-winner-selection'] = 'bidding/run_winner_selection';

/*
|--------------------------------------------------------------------------
| Developer API Key Management
|--------------------------------------------------------------------------
*/
$route['developer'] = 'developer/index';
$route['developer/generate-key'] = 'developer/generate_key';
$route['developer/revoke-key/(:num)'] = 'developer/revoke_key/$1';

/*
|--------------------------------------------------------------------------
| Public Developer API Documentation
|--------------------------------------------------------------------------
*/
$route['api-docs'] = 'api_docs';
$route['api-spec.json'] = 'api_docs/spec';

/*
|--------------------------------------------------------------------------
| Public Developer API Endpoints
|--------------------------------------------------------------------------
| Main protected endpoint for client access via Bearer token.
| Optional public endpoint may be kept only for demo/testing.
*/
$route['api/featured-today'] = 'api/featured_today';
$route['api/featured-today-public'] = 'api/featured_today_public';