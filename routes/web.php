<?php

use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WidgetsController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionController;
require __DIR__ . '/auth.php';

Route::get('/', function () {
    return to_route('login');
});



   
Route::group(['middleware' => ['auth', 'verified']], function () {
    // Dashboards
    Route::get('dashboard-analytic', [HomeController::class, 'analyticDashboard'])->name('dashboards.analytic');
    Route::prefix('events')->group(function () {
        Route::get('/json', [EventController::class, 'index'])->name('events.json');
        Route::get('/', [EventController::class, 'showList'])->name('events.list');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::get('/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/{id}', [EventController::class, 'update'])->name('events.update');
        Route::post('/{id}/status', [EventController::class, 'updateStatus'])->name('events.status');
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/store', [TransactionController::class, 'store'])->name('store');
    });

     Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');


    // Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::get('/crm-dashboard', function () {
        return view('dashboards.crm-dashboard');
    })->name('dashboards.crm');

    Route::get('/project-dashboard', function () {
        return view('dashboards.project-dashboard');
    })->name('dashboards.project');

    // Locale
    Route::get('setlocale/{locale}', SetLocaleController::class)->name('setlocale');

    // User
    Route::resource('users', UserController::class);
    // Permission
    Route::resource('permissions', PermissionController::class)->except(['show']);
    // Roles
    Route::resource('roles', RoleController::class);
    // Profiles
    Route::resource('profiles', ProfileController::class)->only(['index', 'update'])->parameter('profiles', 'user');
    // Env
    Route::singleton('general-settings', GeneralSettingController::class);
    Route::post('general-settings-logo', [GeneralSettingController::class, 'logoUpdate'])->name('general-settings.logo');

    //APPS
    Route::get('chat', [AppsController::class, 'chat'])->name('chat');
 
    Route::get('kanban', [AppsController::class, 'kanban'])->name('kanban');
   
    Route::get('todo', [AppsController::class, 'todo'])->name('todo');
    Route::get('project', [AppsController::class, 'projects'])->name('project');
    Route::get('project-details', [AppsController::class, 'projectDetails'])->name('project-details');

    // UTILITY
    Route::get('utility-invoice', [UtilityController::class, 'invoice'])->name('utility.invoice');
    Route::get('utility-pricing', [UtilityController::class, 'pricing'])->name('utility.pricing');
    Route::get('utility-blog', [UtilityController::class, 'blog'])->name('utility.blog');
    Route::get('utility-blog-details', [UtilityController::class, 'blogDetails'])->name('utility.blog-details');
    Route::get('utility-blank', [UtilityController::class, 'blank'])->name('utility.blank');
    Route::get('utility-settings', [UtilityController::class, 'settings'])->name('utility.settings');
    Route::get('utility-404', [UtilityController::class, 'error404'])->name('utility.404');


    // ELEMENTS
    Route::get('widget-basic', [WidgetsController::class, 'basic'])->name('widget.basic');
    Route::get('widget-statistic', [WidgetsController::class, 'statistic'])->name('widget.statistic');
    Route::get('components-typography', [ComponentsController::class, 'typography'])->name('components.typography');
    Route::get('components-colors', [ComponentsController::class, 'color'])->name('components.colors');
    Route::get('components-alert', [ComponentsController::class, 'alert'])->name('components.alert');
    Route::get('components-button', [ComponentsController::class, 'button'])->name('components.button');
    Route::get('components-card', [ComponentsController::class, 'card'])->name('components.card');
    Route::get('components-carousel', [ComponentsController::class, 'carousel'])->name('components.carousel');
    Route::get('components-dropdown', [ComponentsController::class, 'dropdown'])->name('components.dropdown');
    Route::get('components-image', [ComponentsController::class, 'image'])->name('components.image');
    Route::get('components-modal', [ComponentsController::class, 'modal'])->name('components.modal');
    Route::get('components-progress-bar', [ComponentsController::class, 'progressBar'])->name('components.progress-bar');
    Route::get('components-placeholder', [ComponentsController::class, 'placeholder'])->name('components.placeholder');
    Route::get('components-tab', [ComponentsController::class, 'tab'])->name('components.tab');
    Route::get('components-badges', [ComponentsController::class, 'badges'])->name('components.badges');
    Route::get('components-pagination', [ComponentsController::class, 'pagination'])->name('components.pagination');
    Route::get('components-video', [ComponentsController::class, 'video'])->name('components.video');
    Route::get('components-tooltip', [ComponentsController::class, 'tooltip'])->name('components.tooltip');
    // FORMS
    Route::get('forms-input', [FormController::class, 'input'])->name('forms.input');
    Route::get('input-group', [FormController::class, 'group'])->name('forms.input-group');
    Route::get('input-layout', [FormController::class, 'layout'])->name('forms.input-layout');
    Route::get('forms-input-validation', [FormController::class, 'validation'])->name('forms.input-validation');
    Route::get('forms-input-wizard', [FormController::class, 'wizard'])->name('forms.input-wizard');
    Route::get('forms-input-mask', [FormController::class, 'inputMask'])->name('forms.input-mask');
    Route::get('forms-file-input', [FormController::class, 'fileInput'])->name('forms.file-input');
    Route::get('forms-repeater', [FormController::class, 'repeater'])->name('forms.repeater');
    Route::get('forms-textarea', [FormController::class, 'textarea'])->name('forms.textarea');
    Route::get('forms-checkbox', [FormController::class, 'checkbox'])->name('forms.checkbox');
    Route::get('forms-radio', [FormController::class, 'radio'])->name('forms.radio');
    Route::get('forms-switch', [FormController::class, 'switch'])->name('forms.switch');
    Route::get('forms-select', [FormController::class, 'select'])->name('forms.select');
    Route::get('forms-date-time-picker', [FormController::class, 'dateTimePicker'])->name('forms.date-time-picker');

   
    // CHART
    Route::get('chart', [ChartController::class, 'index'])->name('chart.index');
    Route::get('chart-apex', [ChartController::class, 'apexchart'])->name('chart.apex');

    // Database Backup
    Route::resource('database-backups', DatabaseBackupController::class);
    Route::get('database-backups-download/{fileName}', [DatabaseBackupController::class, 'databaseBackupDownload'])->name('database-backups.download');
});
