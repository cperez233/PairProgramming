<?php

use App\Http\Controllers\SessionController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class , 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class , 'login']);
    Route::get('/register', [RegisterController::class , 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class , 'register']);
});

Route::post('/logout', [LoginController::class , 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/app', [SessionController::class , 'index'])->name('pair.index');
    Route::post('/session/create', [SessionController::class , 'create'])->name('pair.create');
    Route::post('/session/join', [SessionController::class , 'join'])->name('pair.join');

    Route::match (['post', 'patch'], '/room/{code}/swap', [SessionController::class , 'swap'])->name('pair.swap');
    Route::get('/room/{code}', [SessionController::class , 'room'])->name('pair.room');
});

Route::get('/room/{code}/poll', [SessionController::class , 'poll'])->name('pair.poll');
Route::post('/room/{code}/thread', [SessionController::class , 'saveThread'])->name('pair.thread');
Route::post('/room/{code}/chat', [SessionController::class , 'saveChat'])->name('pair.chat.save');
Route::get('/room/{code}/chat', [SessionController::class , 'loadChat'])->name('pair.chat.load');

Route::match (['post', 'patch'], '/room/{code}/swap', [SessionController::class , 'swap'])->name('pair.swap');
Route::get('/room/{code}', [SessionController::class , 'room'])->name('pair.room');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'es'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');
