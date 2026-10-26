<?php

use App\Http\Controllers\Auth\ConsoleSessionController;
use App\Http\Controllers\Auth\DeployerSessionController;
use App\Http\Controllers\Coach\ClientController;
use App\Http\Controllers\Coach\ContactInfoController;
use App\Http\Controllers\Coach\MessageController;
use App\Http\Controllers\Coach\NoteController;
use App\Http\Controllers\Company\CoachController;
use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\Company\ReassignController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\Deployer\CompanyController;
use App\Http\Controllers\Deployer\HomeController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect(auth()->user()->homeRoute())
    : redirect()->route('sign-in'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('deployer/sign-in', [DeployerSessionController::class, 'create'])->name('deployer.sign-in');
    Route::post('deployer/sign-in', [DeployerSessionController::class, 'store'])->middleware('throttle:10,1');
    Route::get('sign-in', [ConsoleSessionController::class, 'create'])->name('sign-in');
    Route::post('sign-in', [ConsoleSessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::post('sign-out', [ConsoleSessionController::class, 'destroy'])->middleware('auth')->name('sign-out');

Route::put('theme', ThemeController::class)->middleware('throttle:60,1')->name('theme.update');

Route::middleware('signed')->group(function () {
    Route::get('consent/{reassignment:token}', [ConsentController::class, 'show'])->name('consent.show');
    Route::post('consent/{reassignment:token}', [ConsentController::class, 'store'])->name('consent.store');
});

Route::middleware(['auth', 'role:super_admin'])->prefix('deployer')->name('deployer.')->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('deploy', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('deploy', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::patch('companies/{company}/status', [CompanyController::class, 'toggleStatus'])->name('companies.status');
});

Route::middleware(['auth', 'role:company', 'company.active', 'console.locale'])->prefix('company')->name('company.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('coaches', [CoachController::class, 'index'])->name('coaches.index');
    Route::post('coaches', [CoachController::class, 'store'])->name('coaches.store');
    Route::put('coaches/{coach}', [CoachController::class, 'update'])->name('coaches.update');
    Route::delete('coaches/{coach}', [CoachController::class, 'destroy'])->name('coaches.destroy');
    Route::get('reassign', [ReassignController::class, 'index'])->name('reassign.index');
    Route::get('reassign/{coach}', [ReassignController::class, 'show'])->name('reassign.show');
    Route::post('reassign/{coach}/consent', [ReassignController::class, 'requestConsent'])->name('reassign.consent');
    Route::post('reassign/{coach}', [ReassignController::class, 'store'])->name('reassign.store');
    Route::view('help', 'company.help')->name('help');
});

Route::middleware(['auth', 'role:coach', 'company.active', 'console.locale'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/new', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('clients/{client}/{app}', [ClientController::class, 'app'])
        ->where('app', 'scintilla|targetpro|momentum|lotus|script-builder')
        ->name('clients.app');
    Route::post('clients/{client}/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('contact-info', [ContactInfoController::class, 'edit'])->name('contact-info.edit');
    Route::put('contact-info', [ContactInfoController::class, 'update'])->name('contact-info.update');
    Route::view('help', 'coach.help')->name('help');
});
