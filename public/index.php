<?php
/**
 * Application Entry Point
 */

// Load Config
require_once dirname(__DIR__) . '/config/config.php';

// Load Router
require_once APP_PATH . '/core/Router.php';

use core\Router;

// Initialize Router
$router = new Router();

// ============== CLIENT ROUTES ==============

// Home Routes
$router->get('/', 'HomeController@index');
$router->get('/home/detail/{id}', 'HomeController@detail');
$router->get('/home/search', 'HomeController@search');
$router->get('/home/get-variants/{id}', 'HomeController@getVariants');
$router->get('/home/get-variant/{variantId}', 'HomeController@getVariant');

// Product Routes
$router->get('/product/category/{categoryId}', 'ProductController@category');

// Cart Routes
$router->get('/cart/view', 'CartController@viewCart');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/apply-promotion', 'CartController@applyPromotion');
$router->post('/cart/clear-promotion', 'CartController@clearPromotion');

// Auth Routes
$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/logout', 'AuthController@logout');

// ============== ADMIN ROUTES ==============

// Admin Routes
$router->get('/admin', 'AdminController@index');
$router->get('/admin/products', 'AdminController@products');
$router->get('/admin/add-product', 'AdminController@addProduct');
$router->post('/admin/add-product', 'AdminController@addProduct');
$router->get('/admin/edit-product/{id}', 'AdminController@editProduct');
$router->post('/admin/edit-product/{id}', 'AdminController@editProduct');
$router->get('/admin/delete-product/{id}', 'AdminController@deleteProduct');
$router->get('/admin/orders', 'AdminController@orders');
$router->post('/admin/update-order-status', 'AdminController@updateOrderStatus');
$router->get('/admin/order-details/{id}', 'AdminController@getOrderDetails');

// Promotion Routes (Marketing Management)
$router->get('/admin/promotions', 'PromotionController@index');
$router->get('/admin/promotions/add', 'PromotionController@add');
$router->post('/admin/promotions/add', 'PromotionController@add');
$router->get('/admin/promotions/edit/{id}', 'PromotionController@edit');
$router->post('/admin/promotions/edit/{id}', 'PromotionController@edit');
$router->get('/admin/promotions/delete/{id}', 'PromotionController@delete');
$router->post('/admin/promotions/delete-ajax', 'PromotionController@deleteAjax');

// Dispatch Request
$router->dispatch();
