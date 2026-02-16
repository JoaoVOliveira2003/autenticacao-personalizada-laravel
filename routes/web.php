<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;

Route::middleware('guest')->group(function(){
    Route::get('/login',[AuthController::class,'login'])->name('login');
    Route::post('/login',[authController::class,'authenticate'])->name('authenticate');

    Route::get('/register',[AuthController::class,'register'])->name('register');
    Route::post('/register',[authController::class,'store_user'])->name('store_user');

    Route::get('/new_user_confirmation/{token}',[authController::class,'new_user_confirmation'])->name('new_user_confirmation');
});

Route::middleware('auth')->group(function(){
   Route::get('/home',action: function(){echo 'vc esta com dados cadastrados';})->name('home');
   Route::get('/logout',[authController::class,'logout'])->name('logout');

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














