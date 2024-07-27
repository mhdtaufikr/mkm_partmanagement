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
use App\Http\Controllers\PreventiveController;



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
    Route::get('/repair-parts', [MstPartRepairController::class, 'repairPart'])->name('repairParts');
    Route::post('/repair-parts/store', [MstPartRepairController::class, 'store']);
    Route::put('/repair-parts/update/{id}', [MstPartRepairController::class, 'update']);
    Route::delete('/repair-parts/delete/{id}', [MstPartRepairController::class, 'destroy']);
    Route::get('/get-repair-stock/{partId}', [MstMachinePartController::class, 'getRepairStock']);

     // mstMachine Part
    Route::get('/mst/machine/part', [MstMachinePartController::class, 'index'])->middleware(['checkRole:IT']);
    Route::get('/mst/machine/detail/{id}', [MstMachinePartController::class, 'detail'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/repair', [MstMachinePartController::class, 'repair'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add-part', [MstMachinePartController::class, 'store'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add', [MstMachinePartController::class, 'storeMachine'])->middleware(['checkRole:IT']);


    Route::get('/history', [HistoryController::class, 'index'])->middleware(['checkRole:IT'])->name('history');
     Route::post('/history/store', [HistoryController::class, 'store']);


    Route::get('/get-parts/{machineId}', [HistoryController::class, 'getParts']);

    Route::get('/get-repair-locations/{partId}', [HistoryController::class, 'getRepairLocations']);
    Route::get('/get-op-nos/{line}', [HistoryController::class, 'getOpNos'])->name('getOpNos');
    Route::get('/form/{no_machine}/{date}/{shift}', [HistoryController::class, 'form'])->name('form');
    Route::get('/get-repair-locations-for-part/{part_id}', [HistoryController::class, 'getRepairLocationsForPart']);
    Route::post('/historical-problems/store', [HistoryController::class, 'storehp'])->name('historical');


    //Preventive Maintanance Master
    //Master Mechine
    Route::get('/mst/preventive', [PreventiveController::class, 'index'])->middleware(['checkRole:IT,Super Admin']);
    Route::post('/mst/preventive/store', [PreventiveController::class, 'store'])->middleware(['checkRole:IT,Super Admin']);
    Route::get('/mst/preventive/detail/{id}', [PreventiveController::class, 'detail'])->name('machine.detail')->middleware(['checkRole:IT,Super Admin']);
    Route::post('/mst/checksheet/type/store', [PreventiveController::class, 'storeChecksheet'])->middleware(['checkRole:IT,Super Admin']);
    Route::post('/mst/checksheet/item/store', [PreventiveController::class, 'storeItemChecksheet'])->middleware(['checkRole:IT,Super Admin']);
    Route::delete('/mst/delete/checksheet/{id}', [PreventiveController::class, 'deleteChecksheet'])->middleware(['checkRole:IT,Super Admin']);
    Route::delete('/mst/delete/checksheet/item/{id}', [PreventiveController::class, 'deleteChecksheetItem'])->middleware(['checkRole:IT,Super Admin']);
    Route::patch('/mst/checksheet/update/{id}', [PreventiveController::class, 'updateChecksheet'])->middleware(['checkRole:IT,Super Admin']);
    Route::patch('/mst/checksheet/item/update/{id}', [PreventiveController::class, 'updateChecksheetItem'])->middleware(['checkRole:IT,Super Admin']);
    Route::get('/checksheet/template', [PreventiveController::class, 'template'])->middleware(['checkRole:IT,Super Admin']);
    Route::post('/checksheet/upload', [PreventiveController::class, 'upload'])->middleware(['checkRole:IT,Super Admin']);

    });
