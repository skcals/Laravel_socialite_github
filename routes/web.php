<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;


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

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/auth/github', function(){
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/github/callback', function(){
    try {
      
        $githubUser = Socialite::driver('github')->user();    
        $user = User::where('github_id', $githubUser->id)->first();
    
        if(!$user){  
            
            $user = User::create([
                'github_id' => $githubUser->id,
                'name' => $githubUser->nickname,
                'email' => $githubUser->email,
                'password'=> Hash::make(rand(0,100))            
            ]);
    
        } 
        Auth::login($user);
        return redirect('/home');
        
    } catch (Exception $e) {
        echo 'Something went wrong try again later.';
    }
});