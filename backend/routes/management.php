 <?php

    use App\Http\Controllers\ManagementController;
    use Illuminate\Support\Facades\Route;


    Route::post('generate_occupancy_rate', [ManagementController::class, 'generateOccupancyRate']);
    Route::get('get_occupancy_rate', [ManagementController::class, 'getOccupancyRate']);
    Route::get('get_single_day_occupancy_rate', [ManagementController::class, 'getSingleDayOccupancyRate']);
    Route::get('get_occupancy_rate_by_month', [ManagementController::class, 'getOccupancyRateByMonth']);