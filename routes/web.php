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
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\KPIDailyReport;



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
Route::post('request/access', [AuthController::class, 'requestAccess']);

Route::middleware(['auth'])->group(function () {
    //Home Controller
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //Dropdown Controller
     Route::get('/dropdown', [DropdownController::class, 'index']);
     Route::post('/dropdown/store', [DropdownController::class, 'store']);
     Route::patch('/dropdown/update/{id}', [DropdownController::class, 'update']);
     Route::delete('/dropdown/delete/{id}', [DropdownController::class, 'delete']);

     //Rules Controller
     Route::get('/rule', [RulesController::class, 'index']);
     Route::post('/rule/store', [RulesController::class, 'store']);
     Route::patch('/rule/update/{id}', [RulesController::class, 'update']);
     Route::delete('/rule/delete/{id}', [RulesController::class, 'delete']);

     //User Controller
     Route::get('/user', [UserController::class, 'index']);
     Route::post('/user/store', [UserController::class, 'store']);
     Route::post('/user/store-partner', [UserController::class, 'storePartner']);
     Route::patch('/user/update/{user}', [UserController::class, 'update']);
     Route::get('/user/revoke/{user}', [UserController::class, 'revoke']);
     Route::get('/user/access/{user}', [UserController::class, 'access']);

     //mstSAP Part
     Route::get('/mst/sap/part/{plnt?}', [MstPartSAPController::class, 'sapPart'])->name('mst.sap.part');
     Route::get('/mst/sap/part/info/{id}', [MstPartSAPController::class, 'sapPartDetail']);
     Route::get('/mst/part/sap/template', [MstPartSAPController::class, 'sapTemplate']);
     Route::post('/mst/part/sap/upload', [MstPartSAPController::class, 'sapUpload']);
     Route::post('/mst/part/add/image', [MstPartSAPController::class, 'addImage']);
     Route::post('/mst/sap/part/store/', [MstPartSAPController::class, 'sapPartDetailStore']);
     Route::post('/mst/sap/part/delete', [MstPartSAPController::class, 'sapPartDelete']);
     Route::get('/part/info', [MstPartSAPController::class, 'partInfo'])->name('part.info');





    // Repair Parts
    Route::get('/repair-parts', [MstPartRepairController::class, 'repairPart'])->name('repairParts');
    Route::post('/repair-parts/store', [MstPartRepairController::class, 'store']);
    Route::put('/repair-parts/update/{id}', [MstPartRepairController::class, 'update']);
    Route::delete('/repair-parts/delete/{id}', [MstPartRepairController::class, 'destroy']);
    Route::get('/get-repair-stock/{partId}', [MstMachinePartController::class, 'getRepairStock']);
    Route::get('/mst/repair/part', [MstPartRepairController::class, 'repairPart']);
     // mstMachine Part
     Route::get('/mst/machine/part/{location?}', [MstMachinePartController::class, 'index'])->name('mst.machine.part');
    Route::get('/mst/machine/detail/{id}', [MstMachinePartController::class, 'detail']);
    Route::post('/mst/machine/repair', [MstMachinePartController::class, 'repair']);
    Route::post('/mst/machine/add-part', [MstMachinePartController::class, 'store']);
    Route::post('/mst/machine/add', [MstMachinePartController::class, 'storeMachine']);
    Route::get('/mst/machine/template', [MstMachinePartController::class, 'machineTemplate']);
    Route::post('/mst/machine/upload', [MstMachinePartController::class, 'machineUpload']);
    Route::get('/mst/part/template', [MstMachinePartController::class, 'partTemplate']);
    Route::post('/mst/part/upload', [MstMachinePartController::class, 'partUpload']);
    Route::post('/mst/machine/add/image', [MstMachinePartController::class, 'addImage']);
    Route::post('mst/machine/delete/image', [MstMachinePartController::class, 'deleteImage'])->name('machine.delete.image');
    Route::post('/mst/machine/delete', [MstMachinePartController::class, 'deleteMachine']);
    Route::put('/spare-parts/{machineId}/update', [MstMachinePartController::class, 'update'])->name('spare-parts.update');


    Route::get('/history', [HistoryController::class, 'index'])->name('history');
     Route::post('/history/store', [HistoryController::class, 'store']);
     Route::get('/history/detail/{id}', [HistoryController::class, 'showDetail']);
     Route::post('/history/upload', [HistoryController::class, 'uploadBulk']);
     Route::get('/history/template', [HistoryController::class, 'templateBulk']);

    Route::get('/get-parts/{machineId}', [HistoryController::class, 'getParts']);

    Route::get('/get-repair-locations/{partId}', [HistoryController::class, 'getRepairLocations']);
    Route::get('/get-op-nos/{line}', [HistoryController::class, 'getOpNos'])->name('getOpNos');
    Route::get('/form', [HistoryController::class, 'form'])->name('form');
    Route::get('/form/update/{id}', [HistoryController::class, 'formUpdate'])->name('formUpdate');


    Route::get('/get-repair-locations-for-part/{part_id}', [HistoryController::class, 'getRepairLocationsForPart']);
    Route::post('/historical-problems/store', [HistoryController::class, 'storehp'])->name('historical');
    Route::post('/historical-problems/store/status', [HistoryController::class, 'storehpStatus'])->name('historicalStatus');
    Route::get('/form/{no_machine}/{date}/{shift}/{id_pm}/{id_checksheet_head}', [HistoryController::class, 'formStatus'])->name('formStatus');
    Route::get('/fetch-parts', [HistoryController::class, 'fetchParts'])->name('fetch.parts');
    Route::get('/get-sap-quantity', [HistoryController::class, 'getSapQuantity'])->name('get.sap.quantity');


    //Preventive Maintanance Master
    //Master Mechine
    Route::get('/mst/preventive', [PreventiveController::class, 'index']);
    Route::post('/mst/preventive/store', [PreventiveController::class, 'store']);
    Route::get('/mst/preventive/detail/{id}', [PreventiveController::class, 'detail'])->name('machine.detail');
    Route::post('/mst/checksheet/type/store', [PreventiveController::class, 'storeChecksheet']);
    Route::post('/mst/checksheet/item/store', [PreventiveController::class, 'storeItemChecksheet']);
    Route::delete('/mst/delete/checksheet/{id}', [PreventiveController::class, 'deleteChecksheet']);
    Route::delete('/mst/delete/checksheet/item/{id}', [PreventiveController::class, 'deleteChecksheetItem']);
    Route::patch('/mst/checksheet/update/{id}', [PreventiveController::class, 'updateChecksheet']);
    Route::patch('/mst/checksheet/item/update/{id}', [PreventiveController::class, 'updateChecksheetItem']);
    Route::get('/checksheet/template', [PreventiveController::class, 'template']);
    Route::post('/checksheet/upload', [PreventiveController::class, 'upload']);
    Route::get('/mst/preventive/schedule/{type}', [PreventiveController::class, 'pmSchedule']);
    Route::get('/mst/preventive/schedule/detail/{month}', [PreventiveController::class, 'pmScheduleDetail']);
    Route::get('/annual/schedule/template', [PreventiveController::class, 'scheduleTemplate']);
    Route::post('/annual/schedule/upload', [PreventiveController::class, 'scheduleUpload']);
    Route::put('/annual/schedule/update', [PreventiveController::class, 'updateSchedule'])->name('annual.schedule.update');
    Route::get('/fetch-plants/{type}', [PreventiveController::class, 'fetchPlants']);
    Route::get('/fetch-shops/{type}/{plant}', [PreventiveController::class, 'fetchShops']);
    Route::get('/fetch-opnos/{type}/{plant}/{shop}', [PreventiveController::class, 'fetchOpNos']);
    Route::post('/schedule/store', [PreventiveController::class, 'scheduleStore']);




    //checksheet
    //Master Checksheet form/checksheet/scan
    Route::get('/checksheet', [ChecksheetController::class, 'index'])->name('machine');
    Route::post('/checksheet/scan', [ChecksheetController::class, 'checksheetScan']);
    Route::post('/checksheet/store', [ChecksheetController::class, 'storeHeadForm'])->name('checksheet.store');
    Route::get('/checksheet/fill/{id}', [ChecksheetController::class, 'checksheetfill'])->name('fillForm');

    Route::post('/checksheet/store/detail', [ChecksheetController::class, 'storeDetailForm']);
    Route::get('/checksheet/detail/{id}', [ChecksheetController::class, 'checksheetDetail'])->name('checksheet.detail');
    Route::post('/checksheet/signature', [ChecksheetController::class, 'checksheetSignature']);

    Route::get('/checksheet/approve/{id}', [ChecksheetController::class, 'checksheetApprove']);
    Route::post('/checksheet/approve/store', [ChecksheetController::class, 'checksheetApproveStore']);
    Route::get('checksheet/update/{id}', [ChecksheetController::class, 'checksheetUpdate']);
    Route::post('/checksheet/update/detail', [ChecksheetController::class, 'checksheetUpdateDetail']);

    Route::get('/checksheet/checkher/{id}', [ChecksheetController::class, 'checksheetChecker']);
    Route::post('/checksheet/checker/store', [ChecksheetController::class, 'checksheetCheckerStore']);

    Route::get('checksheet/generate-pdf/{id}', [ChecksheetController::class, 'generatePdf']);
    Route::get('/get-plants', [ChecksheetController::class, 'getPlants'])->name('get.plants');
    Route::get('/get-shops', [ChecksheetController::class, 'getShops'])->name('get.shops');
    Route::get('/get-opNos', [ChecksheetController::class, 'getOpNos'])->name('get.opNos');
    Route::get('/get-machineNames', [ChecksheetController::class, 'getMachineNames'])->name('get.machineNames');
    Route::post('checksheet/change-status', [ChecksheetController::class, 'changeStatus']);


    Route::get('/summary', [SummaryController::class, 'index']);



    //Master Checksheet form/checksheet/scan
    Route::get('/kpi/daily', [KPIDailyReport::class, 'index']);
    Route::get('kpi/daily/data', [KPIDailyReport::class, 'getData'])->name('kpi.daily.data');


    });
