<?php


use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/devices', DeviceController::class);
Route::get('devices', [DeviceController::class, 'index']);
Route::post('update_device_room_fill_status', [DeviceController::class, 'updateDevicRoomFileStatus']);
Route::get('devices_logs', [DeviceController::class, 'getDevicesLogs']);
Route::get('devices_list', [DeviceController::class, 'getDevicesList']);
