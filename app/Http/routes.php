<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Models\Genre;
use App\Models\Movie;
use App\User;
use App\Services\Tmdb;

Route::get('/', function(){
	return view('index');
});

Route::get('/signup', function(){

	$genres = Genre::all();

	return view('signup', [
		'genres' => $genres
	]);
});

Route::post('/signup', function(){

	$validation = User::validate(Request::all());

	if($validation->passes()){

		$user = new User();

		$user->username = Request::input('username');
		$user->email = Request::input('email');
		$user->password = Hash::make(Request::input('password'));
		$user->genre_id = Request::input('genre_id');
		$user->description = Request::input('description');

		$user->save();


		Auth::loginUsingId($user->id);
		return redirect('dashboard');
	}

	return redirect('signup')
		->withInput()
		->withErrors($validation->errors());

});

Route::get('login', function(){
	return view('login');
});

Route::post('login', function(){

	$credentials = [
		'username' => Request::input('username'), 
		'password' => Request::input('password')
	];

	$remember_me = Request::input('remember_me') == 'on' ? true : false;

	if(Auth::attempt($credentials, $remember_me)){
		return redirect('dashboard');
	}

	return redirect('login');
});

Route::get('logout', function(){

	Auth::logout();

	return redirect('login');
});

Route::group(['middleware' => 'auth'], function(){

	Route::get('/dashboard', 'DashboardController@getDashboard');

	Route::post('/dashboard', 'DashboardController@postDashboard');

	Route::get('/favorites', 'FavoritesController@getFavorites');

	Route::post('/favorites', 'FavoritesController@postFavorites');

	Route::post('/changeGenre', 'DashboardController@changeGenre');

	Route::get('/activities', 'ActivitiesController@getActivities');

});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
