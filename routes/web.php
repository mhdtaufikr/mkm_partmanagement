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
use App\Http\Controllers\ChecksheetController;




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
     Route::get('/mst/sap/part/info/{id}', [MstPartSAPController::class, 'sapPartDetail'])->middleware(['checkRole:IT']);
     Route::get('/mst/part/sap/template', [MstPartSAPController::class, 'sapTemplate'])->middleware(['checkRole:IT']);
     Route::post('/mst/part/sap/upload', [MstPartSAPController::class, 'sapUpload'])->middleware(['checkRole:IT']);


    // Repair Parts
    Route::get('/repair-parts', [MstPartRepairController::class, 'repairPart'])->name('repairParts');
    Route::post('/repair-parts/store', [MstPartRepairController::class, 'store']);
    Route::put('/repair-parts/update/{id}', [MstPartRepairController::class, 'update']);
    Route::delete('/repair-parts/delete/{id}', [MstPartRepairController::class, 'destroy']);
    Route::get('/get-repair-stock/{partId}', [MstMachinePartController::class, 'getRepairStock']);
    Route::get('/mst/repair/part', [MstPartRepairController::class, 'repairPart'])->middleware(['checkRole:IT']);
     // mstMachine Part
    Route::get('/mst/machine/part', [MstMachinePartController::class, 'index'])->middleware(['checkRole:IT']);
    Route::get('/mst/machine/detail/{id}', [MstMachinePartController::class, 'detail'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/repair', [MstMachinePartController::class, 'repair'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add-part', [MstMachinePartController::class, 'store'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add', [MstMachinePartController::class, 'storeMachine'])->middleware(['checkRole:IT']);
    Route::get('/mst/machine/template', [MstMachinePartController::class, 'machineTemplate'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/upload', [MstMachinePartController::class, 'machineUpload'])->middleware(['checkRole:IT']);
    Route::get('/mst/part/template', [MstMachinePartController::class, 'partTemplate'])->middleware(['checkRole:IT']);
    Route::post('/mst/part/upload', [MstMachinePartController::class, 'partUpload'])->middleware(['checkRole:IT']);
    Route::post('/mst/machine/add/image', [MstMachinePartController::class, 'addImage'])->middleware(['checkRole:IT']);
    Route::post('mst/machine/delete/image', [MstMachinePartController::class, 'deleteImage'])->name('machine.delete.image');

    Route::get('/history', [HistoryController::class, 'index'])->middleware(['checkRole:IT'])->name('history');
     Route::post('/history/store', [HistoryController::class, 'store']);


    Route::get('/get-parts/{machineId}', [HistoryController::class, 'getParts']);

    Route::get('/get-repair-locations/{partId}', [HistoryController::class, 'getRepairLocations']);
    Route::get('/get-op-nos/{line}', [HistoryController::class, 'getOpNos'])->name('getOpNos');
    Route::get('/form/{no_machine}/{date}/{shift}', [HistoryController::class, 'form'])->name('form');
    Route::get('/get-repair-locations-for-part/{part_id}', [HistoryController::class, 'getRepairLocationsForPart']);
    Route::post('/historical-problems/store', [HistoryController::class, 'storehp'])->name('historical');
    Route::get('/form/{no_machine}/{date}/{shift}/{id_pm}/{id_checksheet_head}', [HistoryController::class, 'formStatus'])->name('formStatus');


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
    Route::get('/mst/preventive/schedule', [PreventiveController::class, 'pmSchedule'])->middleware(['checkRole:IT,Super Admin']);
    Route::get('/mst/preventive/schedule/detail/{month}', [PreventiveController::class, 'pmScheduleDetail'])->middleware(['checkRole:IT,Super Admin']);
    Route::get('/annual/schedule/template', [PreventiveController::class, 'scheduleTemplate'])->middleware(['checkRole:IT,Super Admin']);
    Route::post('/annual/schedule/upload', [PreventiveController::class, 'scheduleUpload'])->middleware(['checkRole:IT,Super Admin']);
    Route::put('/annual/schedule/update', [PreventiveController::class, 'updateSchedule'])->name('annual.schedule.update');
    Route::get('/fetch-plants/{type}', [PreventiveController::class, 'fetchPlants']);
    Route::get('/fetch-shops/{type}/{plant}', [PreventiveController::class, 'fetchShops']);
    Route::get('/fetch-opnos/{type}/{plant}/{shop}', [PreventiveController::class, 'fetchOpNos']);
    Route::post('/schedule/store', [PreventiveController::class, 'scheduleStore']);




    //checksheet
    //Master Checksheet form/checksheet/scan
    Route::get('/checksheet', [ChecksheetController::class, 'index'])->middleware(['checkRole:IT,Super Admin,Approval,Checker,User'])->name('machine');
    Route::post('/checksheet/scan', [ChecksheetController::class, 'checksheetScan'])->middleware(['checkRole:IT,Super Admin,User']);
    Route::post('/checksheet/store', [ChecksheetController::class, 'storeHeadForm'])->name('checksheet.store');
    Route::get('/checksheet/fill/{id}', [ChecksheetController::class, 'checksheetfill'])->middleware(['checkRole:IT,Super Admin,User'])->name('fillForm');

    Route::post('/checksheet/store/detail', [ChecksheetController::class, 'storeDetailForm'])->middleware(['checkRole:IT,Super Admin,User']);
    Route::get('/checksheet/detail/{id}', [ChecksheetController::class, 'checksheetDetail'])->middleware(['checkRole:IT,Approval,Checker,Super Admin,User']);
    Route::post('/checksheet/signature', [ChecksheetController::class, 'checksheetSignature'])->middleware(['checkRole:IT,Super Admin,User']);

    Route::get('/checksheet/approve/{id}', [ChecksheetController::class, 'checksheetApprove'])->middleware(['checkRole:IT,Super Admin,Approval']);
    Route::post('/checksheet/approve/store', [ChecksheetController::class, 'checksheetApproveStore'])->middleware(['checkRole:IT,Super Admin,Approval']);
    Route::get('checksheet/update/{id}', [ChecksheetController::class, 'checksheetUpdate'])->middleware(['checkRole:IT,Super Admin,User']);
    Route::post('/checksheet/update/detail', [ChecksheetController::class, 'checksheetUpdateDetail'])->middleware(['checkRole:IT,Super Admin,User']);

    Route::get('/checksheet/checkher/{id}', [ChecksheetController::class, 'checksheetChecker'])->middleware(['checkRole:IT,Super Admin,Checker']);
    Route::post('/checksheet/checker/store', [ChecksheetController::class, 'checksheetCheckerStore'])->middleware(['checkRole:IT,Super Admin,Checker']);

    Route::get('checksheet/generate-pdf/{id}', [ChecksheetController::class, 'generatePdf'])->middleware(['checkRole:IT,Super Admin,Approval,Checker,User']);
    Route::get('/get-plants', [ChecksheetController::class, 'getPlants'])->name('get.plants');
    Route::get('/get-shops', [ChecksheetController::class, 'getShops'])->name('get.shops');
    Route::get('/get-opNos', [ChecksheetController::class, 'getOpNos'])->name('get.opNos');
    Route::get('/get-machineNames', [ChecksheetController::class, 'getMachineNames'])->name('get.machineNames');
    Route::post('checksheet/change-status', [ChecksheetController::class, 'changeStatus'])->middleware(['checkRole:IT,Super Admin,Checker']);




    });
