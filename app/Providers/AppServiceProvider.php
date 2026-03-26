<?php

namespace App\Providers;

use App\Support\ParkingHours;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');

        View::composer('*', function (\Illuminate\View\View $view): void {
            $view->with([
                'parkingOpen' => ParkingHours::isOpenForIngresso(),
                'parkingHoursLabel' => ParkingHours::label(),
            ]);
        });
    }
}
