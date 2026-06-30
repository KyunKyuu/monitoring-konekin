<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionPaymentController;
use App\Http\Controllers\DevelopmentTargetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IdealPositionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberNoteController;
use App\Http\Controllers\OperatorMonitoringController;
use App\Http\Controllers\PositionCandidateController;
use App\Http\Controllers\ProgressUpdateController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:super_admin,mentor')->group(function () {
        Route::get('members-import-template', [MemberController::class, 'downloadTemplate'])->name('members.import-template');
        Route::post('members-import', [MemberController::class, 'import'])->name('members.import');
        Route::get('member-hierarchy', [MemberController::class, 'hierarchy'])->name('members.hierarchy');
        Route::patch('member-hierarchy/{member}', [MemberController::class, 'updateHierarchy'])->name('members.hierarchy.update');
        Route::get('activities-import-template', [ActivityController::class, 'downloadTemplate'])->name('activities.import-template');
        Route::post('activities-import', [ActivityController::class, 'import'])->name('activities.import');
        Route::resource('members', MemberController::class)->except(['destroy']);
        Route::resource('activities', ActivityController::class)->except(['destroy']);
        Route::get('activities-calendar', [ActivityController::class, 'calendar'])->name('activities.calendar');
        Route::resource('notes', MemberNoteController::class)->except(['show']);
        Route::resource('targets', DevelopmentTargetController::class)->except(['show']);
        Route::resource('progress', ProgressUpdateController::class)->except(['show']);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::get('operator-monitoring', OperatorMonitoringController::class)->name('operators.index');
        Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    });

    Route::middleware('role:super_admin,pengurus_keuangan')->group(function () {
        Route::resource('contributions', ContributionController::class)->except(['show']);
        Route::get('contribution-payments/create', [ContributionPaymentController::class, 'create'])->name('contribution-payments.create');
        Route::post('contribution-payments', [ContributionPaymentController::class, 'store'])->name('contribution-payments.store');
        Route::resource('cash-accounts', CashAccountController::class)->except(['show']);
        Route::resource('cash-transactions', CashTransactionController::class)->except(['show']);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('ideal-positions', IdealPositionController::class)->except(['show']);
        Route::resource('position-candidates', PositionCandidateController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubcategoryController::class)->except(['show']);
    });

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
