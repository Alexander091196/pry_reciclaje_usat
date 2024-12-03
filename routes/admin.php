<?php

use App\Http\Controllers\Admin\ActionsController;
use App\Http\Controllers\Admin\ActivitiesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandmodelController;
use App\Http\Controllers\Admin\HorariesController;
use App\Http\Controllers\admin\MantenimientoController;
use App\Http\Controllers\Admin\OccupantsController;

use App\Http\Controllers\admin\ProgrammingsController;
use App\Http\Controllers\admin\RouteController;
use App\Http\Controllers\admin\RoutezoneController;
use App\Http\Controllers\admin\ScheduleController;
use App\Http\Controllers\admin\SectorController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\UsertypesController;
use App\Http\Controllers\admin\VehiclecolorsController;

use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\VehicleimagesController;
use App\Http\Controllers\Admin\VehicleOccupantsController;
use App\Http\Controllers\admin\VehicleroutesController;
use App\Http\Controllers\admin\VehicletypesController;
use App\Http\Controllers\admin\ZoneController;
use App\Http\Controllers\admin\ZonecoordController;

Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('models', BrandmodelController::class)->names('admin.models');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
Route::resource('vehicleimages', VehicleimagesController::class)->names('admin.vehicleimages');
Route::get('modelsbybrand/{id}', [BrandmodelController::class, 'modelsbybrand'])->name('admin.modelsbybrand');
Route::get('imageprofile/{id}/{vehicle_id}', [VehicleimagesController::class, 'profile'])->name('admin.imageprofile');
Route::resource('zones', ZoneController::class)->names('admin.zones');
Route::resource('zonecoords', ZonecoordController::class)->names('admin.zonecoords');

Route::resource('vehicletypes', VehicletypesController::class)->names('admin.vehicletypes');

Route::resource('vehiclecolors', VehiclecolorsController::class)->names('admin.vehiclecolors');
Route::resource('usertypes', UsertypesController::class)->names('admin.usertypes');

Route::resource('users', UserController::class)->names('admin.users');
Route::resource('sectors', SectorController::class)->names('admin.sectors');

Route::resource('routes', RouteController::class)->names('admin.routes');
Route::resource('routezones', RoutezoneController::class)->names('admin.routezones');

Route::resource('programming', ProgrammingsController::class)->names('admin.programming');
Route::get('searchprogramming', [ProgrammingsController::class, 'searchprogramming'])->name('admin.searchprogramming');


Route::get('routezones/create/{route_id}', [RoutezoneController::class, 'create'])
    ->name('admin.routezones.create');



Route::resource('/occupants', VehicleOccupantsController::class)->names('admin.vehicleoccupants');
Route::resource('/occupant', OccupantsController::class)->names('admin.occupant');

Route::get('typebyuser/{id}', [VehicleOccupantsController::class, 'typebyuser'])->name('admin.typebyuser');
Route::get('searchbydni/{id}', [OccupantsController::class, 'searchbydni'])->name('admin.searchbydni');
Route::post('occupants/confirm-assignment', [OccupantsController::class, 'confirmAssignment'])->name('admin.occupants.confirm-assignment');

Route::get('/vehicles/{id}', [VehicleController::class, 'showOccupants'])->name('admin.vehicles.occupants');



Route::resource('/activitie', ActivitiesController::class)->names('admin.activities');
Route::resource('/horarie', HorariesController::class)->names('admin.horaries');
Route::resource('/action', ActionsController::class)->names('admin.actions');


Route::resource('schedules', ScheduleController::class)->names('admin.schedules');

Route::resource('vehicleroutes', VehicleroutesController::class)->names('admin.vehicleroutes');

Route::get('admin/vehicleroutes/massupdate/{id}', [VehicleroutesController::class, 'massUpdate'])->name('admin.vehicleroutes.massUpdate');
