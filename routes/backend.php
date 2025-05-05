<?php

use App\Http\Controllers\Web\backend\admin\FAQController;
use App\Http\Controllers\Web\backend\CategoryController;
use App\Http\Controllers\Web\backend\DashboardController;
use App\Http\Controllers\Web\backend\HobbyController;
use App\Http\Controllers\Web\backend\PostController;
use App\Http\Controllers\Web\backend\PremissionController;
use App\Http\Controllers\Web\backend\ProductController;
use App\Http\Controllers\Web\backend\RoleController;
use App\Http\Controllers\Web\backend\SettingController;
use App\Http\Controllers\Web\backend\settings\DynamicPagesController;
use App\Http\Controllers\Web\backend\settings\ProfileSettingController;
use App\Http\Controllers\Web\backend\UserController;
use Illuminate\Support\Facades\Route;

//Dashboard Routes
Route::controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
});
// Settings Route
Route::controller(SettingController::class)->group(function () {
    Route::get('/general/setting', 'create')->name('general.setting');
    Route::post('/system/update', 'update')->name('system.update');
    Route::get('/system/setting', 'systemSetting')->name('system.setting');
    Route::post('/system/setting/update', 'systemSettingUpdate')->name('system.settingupdate');
    Route::get('/setting', 'adminSetting')->name('admin.setting');
    Route::get('/stripe', 'stripe')->name('admin.setting.stripe');
    Route::post('/stripe', 'stripestore')->name('admin.setting.stripestore');
    Route::get('/paypal', 'paypal')->name('admin.setting.paypal');
    Route::post('/paypal', 'paypalstore')->name('admin.setting.paypalstore');
    Route::get('/mail', 'mail')->name('admin.setting.mail');
    Route::post('/mail', 'mailstore')->name('admin.setting.mailstore');
    Route::post('/setting/update', 'adminSettingUpdate')->name('admin.settingupdate');
});

// Profile Settings Controller
Route::controller(ProfileSettingController::class)->group(function () {
    Route::get('/profile', 'index')->name('profile');
    Route::post('/profile/update', 'updateProfile')->name('profile.update');
    Route::post('/profile/update/password', 'updatePassword')->name('profile.update.password');
    Route::post('/profile/update/profile-picture', 'updateProfilePicture')->name('profile.update.profile.picture');
    Route::get('/checkusername', 'checkusername')->name('checkusername');
});

// FAQ Route
Route::controller(FAQController::class)->group(function () {
    Route::get('/faq', 'index')->name('faq.index');
    Route::get('/faq/get', 'get')->name('faq.get');
    Route::post('/faq/priorities', 'priority')->name('faq.priority');
    Route::get('/faq/status', 'status')->name('faq.status');
    Route::post('/faq/store', 'store')->name('faq.store');
    Route::post('/faq/update', 'update')->name('faq.update');
    Route::get('/faq/destroy/{id}', 'destroy')->name('faq.destroy');
});

// Hobby Route
Route::controller(HobbyController::class)->group(function () {
    Route::get('/hobby', 'index')->name('hobby.index');
    Route::get('/hobby/get', 'get')->name('hobby.get');
    Route::post('/hobby/priorities', 'priority')->name('hobby.priority');
    Route::get('/hobby/status', 'status')->name('hobby.status');
    Route::post('/hobby/store', 'store')->name('hobby.store');
    Route::post('/hobby/update', 'update')->name('hobby.update');
    Route::get('/hobby/destroy/{id}', 'destroy')->name('hobby.destroy');
});

// User Route
Route::controller(UserController::class)->group(function () {
    Route::get('/users/create', 'create')->name('user.create');
    Route::post('/users/store', 'store')->name('user.store');
    Route::get('/edit/users/{id}', 'edit')->name('user.edit');
    Route::post('/update/user', 'update')->name('user.update');
    Route::get('/users/list', 'index')->name('user.list');
    Route::get('/view/users/{id}', 'show')->name('show.user');
    Route::get('/status/users/{id}', 'status')->name('user.status');
    Route::post('/users/delete', 'destroy')->name('user.user.destroy');
});

//Dynamic Pages Route
Route::controller(DynamicPagesController::class)->group(function () {
    Route::get('/dynamicpages', 'index')->name('dynamicpages.index');
    Route::get('/dynamicpages/create', 'create')->name('dynamicpages.create');
    Route::get('/dynamicpages/edit/{id}', 'edit')->name('dynamicpages.edit');
    Route::post('/dynamicpages/store', 'store')->name('dynamicpages.store');
    Route::post('/dynamicpages/update/{id}', 'update')->name('dynamicpages.update');
    Route::delete('/dynamicpages/destroy/{id}', 'destroy')->name('dynamicpages.destroy');
    Route::post('/dynamicpages/status/{id}', 'changeStatus')->name('dynamicpages.status');
    Route::post('/dynamicpages/bulk-delete', 'bulkDelete')->name('dynamicpages.bulk-delete');
});

//Post Pages Route
Route::controller(PostController::class)->group(function () {
    Route::get('/post', 'index')->name('post.index');
    Route::delete('/post/destroy/{id}', 'destroy')->name('post.destroy');
    Route::post('/post/status/{id}', 'changeStatus')->name('post.status');
    Route::post('/post/bulk-delete', 'bulkDelete')->name('post.bulk-delete');
});

Route::prefix('permissions')->controller(PremissionController::class)->group(function () {
    Route::get('/list', 'index')->name('admin.permissions.list');
    Route::get('/create', 'create')->name('admin.permissions.create');
});

Route::prefix('role')->controller(RoleController::class)->group(function () {
    Route::get('/list', 'index')->name('admin.role.list');
    Route::get('/create', 'create')->name('admin.role.create');
    Route::post('/store', 'store')->name('admin.role.store');
    Route::get('/edit/{id}', 'edit')->name('admin.role.edit');
    Route::post('/update/{id}', 'update')->name('admin.role.update');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.role.destroy');
});
