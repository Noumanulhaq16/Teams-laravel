<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('superadmin')->group(function () {
    Route::post('login', [App\Http\Controllers\SuperAdminController::class, 'SuperAdminLogin'])->name('superadmin.login');
    Route::post('forgotpassword', [App\Http\Controllers\SuperAdminController::class, 'SuperAdminForgetPassword'])->name('superadmin.forgotpassword');
    Route::post('resetpassword', [App\Http\Controllers\SuperAdminController::class, 'SuperAdminResetPassword'])->name('superadmin.resetpassword');
    // CUSTOMER APPROVEL REQUSET
    Route::get('agent', [App\Http\Controllers\SuperAdminController::class, 'Agent'])->name('superadmin.agent');
    Route::post('approveagent/{id}', [App\Http\Controllers\SuperAdminController::class, 'AgentApprove'])->name('superadmin.approve.agent');
    // APPROVE CONTRACTOR
    Route::post('approvesalesman/{id}', [App\Http\Controllers\SuperAdminController::class, 'SalemanApprove'])->name('superadmin.approve.salesman');
});


Route::prefix('admin')->group(function () {
    Route::post('login', [App\Http\Controllers\AdminController::class, 'AdminLogin'])->name('admin.login');
});

Route::prefix('agent')->group(function () {
    Route::post('login', [App\Http\Controllers\AgentController::class, 'AgentLogin'])->name('agent.login');
    Route::post('register', [App\Http\Controllers\AgentController::class, 'AgentRegister'])->name('agent.register');
});




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
