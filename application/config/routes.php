<?php
defined('BASEPATH') or exit('No direct script access allowed');
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Custom routes untuk mempermudah akses
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['dashboard'] = 'dashboard';
$route['setup'] = 'setup';
$route['aktifitas'] = 'aktifitas';
$route['daftar'] = 'daftar';
$route['laporan'] = 'laporan';
$route['pengaturan'] = 'pengaturan';