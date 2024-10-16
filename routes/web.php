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

    // Home Controller (accessible to all roles)
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['checkRole:IT,Admin,Leader,User']);

    // Dropdown Controller
    Route::get('/dropdown', [DropdownController::class, 'index'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/dropdown/store', [DropdownController::class, 'store'])->middleware(['checkRole:IT']);
    Route::patch('/dropdown/update/{id}', [DropdownController::class, 'update'])->middleware(['checkRole:IT']);
    Route::delete('/dropdown/delete/{id}', [DropdownController::class, 'delete'])->middleware(['checkRole:IT']);

    // Rules Controller (IT, Admin can manage)
    Route::get('/rule', [RulesController::class, 'index'])->middleware(['checkRole:IT,Admin']);
    Route::post('/rule/store', [RulesController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::patch('/rule/update/{id}', [RulesController::class, 'update'])->middleware(['checkRole:IT,Admin']);
    Route::delete('/rule/delete/{id}', [RulesController::class, 'delete'])->middleware(['checkRole:IT,Admin']);

    // User Controller (full access for IT and Admin)
    Route::get('/user', [UserController::class, 'index'])->middleware(['checkRole:IT,Admin']);
    Route::post('/user/store', [UserController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::post('/user/store-partner', [UserController::class, 'storePartner'])->middleware(['checkRole:IT,Admin']);
    Route::patch('/user/update/{user}', [UserController::class, 'update'])->middleware(['checkRole:IT,Admin']);
    Route::get('/user/revoke/{user}', [UserController::class, 'revoke'])->middleware(['checkRole:IT,Admin']);
    Route::get('/user/access/{user}', [UserController::class, 'access'])->middleware(['checkRole:IT,Admin']);

    // Master SAP Part (Admin and IT for create/update, read for all roles)
    Route::get('/mst/sap/part/{plnt?}', [MstPartSAPController::class, 'sapPart'])->name('mst.sap.part')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/mst/sap/part/info/{id}', [MstPartSAPController::class, 'sapPartDetail'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/mst/part/sap/template', [MstPartSAPController::class, 'sapTemplate'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/part/sap/upload', [MstPartSAPController::class, 'sapUpload'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/part/add/image', [MstPartSAPController::class, 'addImage'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/sap/part/store/', [MstPartSAPController::class, 'sapPartDetailStore'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/sap/part/delete', [MstPartSAPController::class, 'sapPartDelete'])->middleware(['checkRole:IT,Admin']);
    Route::get('/part/info', [MstPartSAPController::class, 'partInfo'])->name('part.info')->middleware(['checkRole:IT,Admin,Leader,User']);

    // Repair Parts (Admin, IT for manage; Leader for read)
    Route::get('/repair-parts', [MstPartRepairController::class, 'repairPart'])->name('repairParts')->middleware(['checkRole:IT,Admin,Leader']);
    Route::post('/repair-parts/store', [MstPartRepairController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::put('/repair-parts/update/{id}', [MstPartRepairController::class, 'update'])->middleware(['checkRole:IT,Admin']);
    Route::delete('/repair-parts/delete/{id}', [MstPartRepairController::class, 'destroy'])->middleware(['checkRole:IT,Admin']);

    // Master Machine Part (IT, Admin for manage, Leader/User for read)
    Route::get('/mst/machine/part/{location?}', [MstMachinePartController::class, 'index'])->name('mst.machine.part')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/mst/machine/detail/{id}', [MstMachinePartController::class, 'detail'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/mst/machine/repair', [MstMachinePartController::class, 'repair'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/machine/add-part', [MstMachinePartController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/machine/add', [MstMachinePartController::class, 'storeMachine'])->middleware(['checkRole:IT,Admin']);
    Route::get('/mst/machine/template', [MstMachinePartController::class, 'machineTemplate'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/machine/upload', [MstMachinePartController::class, 'machineUpload'])->middleware(['checkRole:IT,Admin']);

    // History Controller (accessible by all, but create/update for Admin/IT)
    Route::get('/history', [HistoryController::class, 'index'])->name('history')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/history/store', [HistoryController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::get('/history/detail/{id}', [HistoryController::class, 'showDetail'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/history/upload', [HistoryController::class, 'uploadBulk'])->middleware(['checkRole:IT,Admin']);
    Route::get('/history/template', [HistoryController::class, 'templateBulk'])->middleware(['checkRole:IT,Admin']);

    // Preventive Maintenance (Admin and IT for manage, others for read)
    Route::get('/mst/preventive', [PreventiveController::class, 'index'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/mst/preventive/store', [PreventiveController::class, 'store'])->middleware(['checkRole:IT,Admin']);
    Route::get('/mst/preventive/detail/{id}', [PreventiveController::class, 'detail'])->name('machine.detail')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/mst/checksheet/type/store', [PreventiveController::class, 'storeChecksheet'])->middleware(['checkRole:IT,Admin']);
    Route::post('/mst/checksheet/item/store', [PreventiveController::class, 'storeItemChecksheet'])->middleware(['checkRole:IT,Admin']);
    Route::delete('/mst/delete/checksheet/{id}', [PreventiveController::class, 'deleteChecksheet'])->middleware(['checkRole:IT,Admin']);
    Route::delete('/mst/delete/checksheet/item/{id}', [PreventiveController::class, 'deleteChecksheetItem'])->middleware(['checkRole:IT,Admin']);

    // Checksheet Controller (Admin and IT for manage, others for read)
    Route::get('/checksheet', [ChecksheetController::class, 'index'])->name('machine')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/checksheet/scan', [ChecksheetController::class, 'checksheetScan'])->middleware(['checkRole:IT,Admin']);
    Route::post('/checksheet/store', [ChecksheetController::class, 'storeHeadForm'])->name('checksheet.store')->middleware(['checkRole:IT,Admin']);
    Route::get('/checksheet/fill/{id}', [ChecksheetController::class, 'checksheetfill'])->name('fillForm')->middleware(['checkRole:IT,Admin,Leader,User']);
    // Preventive Maintenance Master
    Route::post('/checksheet/store/detail', [ChecksheetController::class, 'storeDetailForm'])->middleware(['checkRole:IT,Admin']);
    Route::get('/checksheet/detail/{id}', [ChecksheetController::class, 'checksheetDetail'])->name('checksheet.detail')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('/checksheet/signature', [ChecksheetController::class, 'checksheetSignature'])->middleware(['checkRole:IT,Admin']);

    Route::get('/checksheet/approve/{id}', [ChecksheetController::class, 'checksheetApprove'])->middleware(['checkRole:IT,Admin']);
    Route::post('/checksheet/approve/store', [ChecksheetController::class, 'checksheetApproveStore'])->middleware(['checkRole:IT,Admin']);
    Route::get('checksheet/update/{id}', [ChecksheetController::class, 'checksheetUpdate'])->middleware(['checkRole:IT,Admin']);
    Route::post('/checksheet/update/detail', [ChecksheetController::class, 'checksheetUpdateDetail'])->middleware(['checkRole:IT,Admin']);

    Route::get('/checksheet/checkher/{id}', [ChecksheetController::class, 'checksheetChecker'])->middleware(['checkRole:IT,Admin']);
    Route::post('/checksheet/checker/store', [ChecksheetController::class, 'checksheetCheckerStore'])->middleware(['checkRole:IT,Admin']);

    Route::get('checksheet/generate-pdf/{id}', [ChecksheetController::class, 'generatePdf'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/get-plants', [ChecksheetController::class, 'getPlants'])->name('get.plants')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/get-shops', [ChecksheetController::class, 'getShops'])->name('get.shops')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/get-opNos', [ChecksheetController::class, 'getOpNos'])->name('get.opNos')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/get-machineNames', [ChecksheetController::class, 'getMachineNames'])->name('get.machineNames')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('checksheet/change-status', [ChecksheetController::class, 'changeStatus'])->middleware(['checkRole:IT,Admin']);

    // Summary Controller (read access for all roles)
    Route::get('/summary', [SummaryController::class, 'index'])->name('summary.data')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('/summary/detail/{id}', [SummaryController::class, 'showDetail'])->middleware(['checkRole:IT,Admin,Leader,User']);

    // KPI Daily Report (Admin, IT for create/update, read for all roles)
    Route::get('/kpi/daily', [KPIDailyReport::class, 'index'])->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::get('kpi/daily/data', [KPIDailyReport::class, 'getData'])->name('kpi.daily.data')->middleware(['checkRole:IT,Admin,Leader,User']);
    Route::post('kpi/daily/data/update', [KPIDailyReport::class, 'update'])->name('kpi.daily.data.update')->middleware(['checkRole:IT,Admin']);
    Route::get('kpi/daily/data/child/{id}', [KPIDailyReport::class, 'getChildData'])->middleware(['checkRole:IT,Admin,Leader,User']);
});
