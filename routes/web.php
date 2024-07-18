<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MstPartSAPController;
use App\Http\Controllers\MstMachinePartController;
use App\Http\Controllers\MstPartRepairController;
use App\Http\Controllers\HistoryController;


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

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'postLogin']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth'])->group(function () {
    //Home Controller
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //Dropdown Controller
     Route::get('/dropdown', [DropdownController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/dropdown/store', [DropdownController::class, 'store'])->middleware(['checkRole:IT']);
     Route::patch('/dropdown/update/{id}', [DropdownController::class, 'update'])->middleware(['checkRole:IT']);
     Route::delete('/dropdown/delete/{id}', [DropdownController::class, 'delete'])->middleware(['checkRole:IT']);

     //Rules Controller
     Route::get('/rule', [RulesController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/rule/store', [RulesController::class, 'store'])->middleware(['checkRole:IT']);
     Route::patch('/rule/update/{id}', [RulesController::class, 'update'])->middleware(['checkRole:IT']);
     Route::delete('/rule/delete/{id}', [RulesController::class, 'delete'])->middleware(['checkRole:IT']);

     //User Controller
     Route::get('/user', [UserController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/user/store', [UserController::class, 'store'])->middleware(['checkRole:IT']);
     Route::post('/user/store-partner', [UserController::class, 'storePartner'])->middleware(['checkRole:IT']);
     Route::patch('/user/update/{user}', [UserController::class, 'update'])->middleware(['checkRole:IT']);
     Route::get('/user/revoke/{user}', [UserController::class, 'revoke'])->middleware(['checkRole:IT']);
     Route::get('/user/access/{user}', [UserController::class, 'access'])->middleware(['checkRole:IT']);

     //mstSAP Part
     Route::get('/mst/sap/part', [MstPartSAPController::class, 'sapPart'])->middleware(['checkRole:IT']);
     Route::get('/mst/repair/part', [MstPartRepairController::class, 'repairPart'])->middleware(['checkRole:IT']);
// Repair Parts
Route::get('/repair-parts', [RepairPartController::class, 'repairPart'])->name('repairParts');
Route::post('/repair-parts/store', [RepairPartController::class, 'store']);
Route::put('/repair-parts/update/{id}', [RepairPartController::class, 'update']);
Route::delete('/repair-parts/delete/{id}', [RepairPartController::class, 'destroy']);
Route::get('/get-repair-stock/{partId}', [MstMachinePartController::class, 'getRepairStock']);

     // mstMachine Part
    Route::get('/mst/machine/part', [MstMachinePartController::class, 'index'])->middleware(['checkRole:IT']);
    Route::get('/mst/machine/detail/{id}', [MstMachinePartController::class, 'detail'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/repair', [MstMachinePartController::class, 'repair'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add-part', [MstMachinePartController::class, 'store'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add', [MstMachinePartController::class, 'storeMachine'])->middleware(['checkRole:IT']);


     Route::get('/history', [HistoryController::class, 'index'])->middleware(['checkRole:IT']);
     Route::post('/history/store', [HistoryController::class, 'store']);
    // History routes
    Route::get('/history/spare-parts/{id}', [HistoryController::class, 'getSpareParts'])->middleware(['checkRole:IT']);
    Route::get('/get-parts/{machineId}', [HistoryController::class, 'getParts']);
    Route::get('/get-locations/{partId}', [HistoryController::class, 'getLocations']);

    });
