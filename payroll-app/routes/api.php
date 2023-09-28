<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\PayrollController;

Route::post('/employees', [PayrollController::class, 'createEmployee']);
Route::post('/employees/{employeeId}/transactions', [PayrollController::class, 'submitTransaction']);
Route::get('/unpaid-salaries', [PayrollController::class, 'unpaidSalaries']);
Route::post('/pay-all-salaries', [PayrollController::class, 'payAllSalaries']);

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
?>
