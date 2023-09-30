<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\PayrollController;

Route::get('/payroll', 'App\Http\Controllers\PayrollController@index');
Route::post('/employees', ['App\Http\Controllers\PayrollController', 'createEmployee']);
Route::post('/employees/{employeeId}/transactions', ['App\Http\Controllers\PayrollController', 'submitTransaction']);
Route::get('/unpaid-salaries', ['App\Http\Controllers\PayrollController', 'unpaidSalaries']);
Route::post('/pay-all-salaries', ['App\Http\Controllers\PayrollController', 'payAllSalaries']);
Route::get('/employee-payments', 'App\Http\Controllers\PayrollController@employeePayments');


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
