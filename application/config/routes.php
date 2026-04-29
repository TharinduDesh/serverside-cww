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


/*
|--------------------------------------------------------------------------
| University Analytics API Endpoints - CW2
|--------------------------------------------------------------------------
*/
$route['api/analytics/summary'] = 'analytics_api/summary';
$route['api/analytics/programmes'] = 'analytics_api/programmes';
$route['api/analytics/graduation-years'] = 'analytics_api/graduation_years';
$route['api/analytics/industry-sectors'] = 'analytics_api/industry_sectors';
$route['api/analytics/job-titles'] = 'analytics_api/job_titles';
$route['api/analytics/certifications'] = 'analytics_api/certifications';
$route['api/analytics/courses'] = 'analytics_api/courses';
$route['api/analytics/top-employers'] = 'analytics_api/top_employers';
$route['api/analytics/geography'] = 'analytics_api/geography';
$route['api/analytics/skills-gap'] = 'analytics_api/skills_gap';
$route['api/analytics/filter-options'] = 'analytics_api/filter_options';
$route['api/analytics/alumni'] = 'analytics_api/alumni';

/*
|--------------------------------------------------------------------------
| University Analytics Dashboard - CW2
|--------------------------------------------------------------------------
*/
$route['university'] = 'university/dashboard';
$route['university/dashboard'] = 'university/dashboard';
$route['university/graphs'] = 'university/graphs';
$route['university/alumni'] = 'university/alumni';
$route['university/reports'] = 'university/reports';

/*
|--------------------------------------------------------------------------
| Export CSV Routes - CW2
|--------------------------------------------------------------------------
*/
$route['export/alumni-csv'] = 'export/alumni_csv';
$route['export/analytics-summary-csv'] = 'export/analytics_summary_csv';