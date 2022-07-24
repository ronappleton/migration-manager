<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'personal'], static function (Router $router): void {
    $router->post('register', 'Register@register')->name('user.personal.register');
});

Route::group(['prefix' => 'business'], static function (Router $router): void {
    $router->post('register', 'Register@registerBusiness')->name('user.personal.register.business');
});
