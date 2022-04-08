<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollHandler;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function(){return "Bulacan State University - Team 1";});
Route::get('/management/employee', [EmployeeController::class, 'getEmp']);
Route::get('/management/employee/{id}', [EmployeeController::class, 'getEmpById']);
Route::delete('/management/employee/{id}', [EmployeeController::class, 'delEmpById']);
Route::put('/management/employee/{id}', [EmployeeController::class, 'putEmpById']);
Route::post('/management/employee/', [EmployeeController::class, 'saveEmp']);


Route::post('/management/employee/{id}/salary', [PayrollHandler::class, 'getSalary']);
Route::post('/management/employee/{id}/timein', [PayrollHandler::class, 'timeIn']);
Route::post('/management/employee/{id}/timeout', [PayrollHandler::class, 'timeOut']);
Route::post('/management/employee/{id}/leave', [PayrollHandler::class, 'leave']);
Route::get('/test/{date}', [PayrollHandler::class, 'test']);