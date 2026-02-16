<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\MainController;

Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'login'])->name('login');
    Route::post('/login',[authController::class,'authenticate'])->name('authenticate');

    Route::get('/register',[AuthController::class,'register'])->name('register');
    Route::post('/register',[authController::class,'store_user'])->name('store_user');

    Route::get('/new_user_confirmation/{token}',[authController::class,'new_user_confirmation'])->name('new_user_confirmation');

    Route::get('/forget_password',[authController::class,'forgot_password'])->name('forgot_password');
    Route::post('/forget_password',[authController::class,'send_reset_link'])->name('send_reset_link');



});

Route::middleware('auth')->group(function(){
   Route::get('/',[MainController::class,'home'])->name('home');

   Route::get('/profile',[AuthController::class,'profile'])->name('profile');
   Route::post('/profile',[authController::class,'charge_password'])->name('charge_password');

   Route::get('/logout',[authController::class,'logout'])->name('logout');

    Route::get('/reset_password/{token}',[authController::class,'reset_password'])->name('reset_password');
    Route::post('/reset_password',[authController::class,'atualizacao_password'])->name('atualizacao_password');


    Route::post('/apagarConta',[authController::class,'apagarConta'])->name('apagarConta');
});





























// Route::get('/',function(){
//     if(DB::connection()->getPdo()){
//         echo 'conectpi';
//     }
//     else{
//         'n foi';
//     }
// });
// Route::view('/teste','teste')->middleware('auth');
// Route::get('/login', function(){
//     echo'carai';
// })->name('login');
/*
O auth entra somente para quem tiver sido autenticado, oo guest somente para aqueles que n estão cadastrados
*/














