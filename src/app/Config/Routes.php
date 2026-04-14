<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * --------------------------------------------------------------------
 * Web Routes
 * --------------------------------------------------------------------
 */
$routes->get('/', 'Web::index');
$routes->get('register', 'Web::register');
$routes->get('login', 'Web::login');
$routes->get('dashboard', 'Web::dashboard');
$routes->get('profile', 'Web::profile');

// 言語切り替え
$routes->get('language/switch/(:segment)', 'Language::switch/$1');

/*
 * --------------------------------------------------------------------
 * API Routes
 * --------------------------------------------------------------------
 */

// 公開API（認証不要）
$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    // 認証関連
    $routes->post('auth/register', 'Auth::register');
    $routes->post('auth/login', 'Auth::login');
    
    // 公開エンドポイント
    $routes->get('public', 'Api::public');
    
    // 言語切り替え
    $routes->post('language/switch', 'Language::apiSwitch');
    $routes->get('language/current', 'Language::current');
});

// 保護されたAPI（JWT認証が必要）
$routes->group('api', ['namespace' => 'App\Controllers', 'filter' => 'jwtauth'], function ($routes) {
    // ユーザー情報
    $routes->get('auth/me', 'Auth::me');
    $routes->put('auth/profile', 'Auth::updateProfile');
    
    // 保護されたエンドポイント
    $routes->get('protected', 'Api::protected');
    
    // デバイスデータ
    $routes->post('device/data', 'Api::deviceData');
    $routes->get('device/data', 'Api::getDeviceData');
});
