<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [MigrationManager\Http\Controllers\HomeController::class, 'index']);

Route::get('/dashboard', [MigrationManager\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

# Authentication routes
Route::get('login', 'MigrationManager\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'MigrationManager\Http\Controllers\Auth\LoginController@login');
Route::post('logout', 'MigrationManager\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('register', 'MigrationManager\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'MigrationManager\Http\Controllers\Auth\RegisterController@register');

Route::get('password/reset', 'MigrationManager\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'MigrationManager\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'MigrationManager\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'MigrationManager\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

Route::get('password/confirm', 'MigrationManager\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'MigrationManager\Http\Controllers\Auth\ConfirmPasswordController@confirm');

Route::get('email/verify', 'MigrationManager\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'MigrationManager\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'MigrationManager\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');
