<?php

use Illuminate\Support\Facades\Route;

// Serve Blade views (Frontend for testing API)
Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard', function () {
    return view('employee.dashboard');
})->name('dashboard');

Route::get('/expenses', function () {
    return view('employee.expenses');
})->name('expenses');
