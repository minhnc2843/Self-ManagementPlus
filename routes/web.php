<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\SetLocaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SharedExpenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\DigitalAssetController;
require __DIR__ . '/auth.php';

Route::get('/', function () {
    return to_route('login');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    // Dashboards
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/banner/edit', [DashboardController::class, 'editBannerPage'])->name('dashboard.banner.edit'); // Trang form sửa
    Route::post('/dashboard/update-banner', [DashboardController::class, 'updateBanner'])->name('dashboard.update-banner'); //chưa có

    Route::get('/dashboard/goals/create', [DashboardController::class, 'createGoalPage'])->name('goals.create');
    Route::post('/dashboard/goals', [DashboardController::class, 'storeGoal'])->name('goals.store');
    Route::get('/dashboard/goals/{id}/edit', [DashboardController::class, 'editGoalPage'])->name('goals.edit');
    Route::put('/dashboard/goals/{id}', [DashboardController::class, 'updateGoal'])->name('goals.update');
    Route::patch('/dashboard/goals/{id}/progress', [DashboardController::class, 'updateProgress'])
    ->name('goals.progress');
    Route::patch('/dashboard/goals/{id}/complete', [DashboardController::class, 'completeGoal'])
        ->name('goals.complete');
        Route::patch('/dashboard/goals/{id}/progress', [DashboardController::class, 'updateProgress'])
    ->name('goals.progress');

    Route::get('/dashboard/plans/create', [DashboardController::class, 'createPlanPage'])->name('plans.create');
    Route::post('/dashboard/plans', [DashboardController::class, 'storePlan'])->name('plans.store');// chưa có
    Route::post('/dashboard/plans/{id}/toggle', [DashboardController::class, 'togglePlanStatus'])->name('plans.toggle');// chưa có
    Route::delete('/dashboard/plans/{id}', [DashboardController::class, 'destroyPlan'])->name('plans.destroy');// chưa có
    
    Route::prefix('events')->group(function () {
        Route::get('/json', [EventController::class, 'index'])->name('events.json');
        Route::get('/', [EventController::class, 'showList'])->name('events.list');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/', [EventController::class, 'store'])->name('events.store');
        Route::get('/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/{id}', [EventController::class, 'update'])->name('events.update');
        Route::get('/show/{id}', [EventController::class, 'showDetail'])->name('events.show');// chỉ mới thêm dòng này
        Route::post('/{id}/status', [EventController::class, 'updateStatus'])->name('events.status');
    });

    Route::get('/finance', [TransactionController::class, 'index'])->name('finance.index');
    Route::get('/finance/create', [TransactionController::class, 'create'])->name('finance.create');
    Route::post('/finance/store', [TransactionController::class, 'store'])->name('finance.store');
    Route::post('/finance/loans/store', [TransactionController::class, 'storeLoan'])->name('finance.loans.store');
    Route::post('/finance/loans/{id}/pay', [TransactionController::class, 'payLoan'])->name('finance.loans.pay');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.read');
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::get('expense-groups/{group}/add-expense', [SharedExpenseController::class, 'createExpenseView'])->name('expense-groups.add-expense-view');
    Route::resource('expense-groups', SharedExpenseController::class);
    Route::post('expense-groups/{group}/add-expense', [SharedExpenseController::class, 'storeExpense'])->name('expense-groups.add-expense');

   
    
    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::post('/file-manager/folder', [FileManagerController::class, 'createFolder'])->name('file-manager.create-folder');
    Route::post('/file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
    Route::delete('/file-manager/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
    Route::delete('/file-manager/bulk-delete', [FileManagerController::class, 'bulkDelete'])->name('file-manager.bulk-delete');
    Route::post('/file-manager/rename', [FileManagerController::class, 'rename'])->name('file-manager.rename');
    Route::get('/file-manager/download/{id}', [FileManagerController::class, 'download'])->name('file-manager.download');
    Route::get('/file-manager/content/{id}', [FileManagerController::class, 'getContent'])->name('file-manager.get-content');
    Route::post('/file-manager/save-content/{id}', [FileManagerController::class, 'saveContent'])->name('file-manager.save-content');
    // Locale

    Route::get('/storage/links', [DigitalAssetController::class, 'indexLinks'])->name('assets.links');
    Route::post('/storage/links', [DigitalAssetController::class, 'storeLink'])->name('assets.links.store');
    Route::put('/storage/links/{id}', [DigitalAssetController::class, 'updateLink'])->name('assets.links.update');
    Route::delete('/storage/links/{id}', [DigitalAssetController::class, 'destroyLink'])->name('assets.links.destroy');

    Route::get('/storage/clipboard', [DigitalAssetController::class, 'indexClipboard'])->name('assets.clipboard');
    Route::post('/storage/clipboard', [DigitalAssetController::class, 'storeClipboard'])->name('assets.clipboard.store');
    Route::put('/storage/clipboard/{id}', [DigitalAssetController::class, 'updateClipboard'])->name('assets.clipboard.update');
    Route::delete('/storage/clipboard/{id}', [DigitalAssetController::class, 'destroyClipboard'])->name('assets.clipboard.destroy');
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
    Route::get('kanban', [AppsController::class, 'kanban'])->name('kanban');
   
    Route::get('todo', [AppsController::class, 'todo'])->name('todo');
    Route::get('project', [AppsController::class, 'projects'])->name('project');
    Route::get('project-details', [AppsController::class, 'projectDetails'])->name('project-details');

    // UTILITY
    Route::get('utility-blog', [UtilityController::class, 'blog'])->name('utility.blog');
    Route::get('utility-settings', [UtilityController::class, 'settings'])->name('utility.settings');
    Route::get('utility-404', [UtilityController::class, 'error404'])->name('utility.404');

    // Database Backup
    Route::resource('database-backups', DatabaseBackupController::class);
    Route::get('database-backups-download/{fileName}', [DatabaseBackupController::class, 'databaseBackupDownload'])->name('database-backups.download');
    Route::get('icon', function () {
    return view('elements.icon.icon');
});
});
