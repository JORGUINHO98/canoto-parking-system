<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        /**
         * 1. SEGURIDAD PARA PRODUCCIÓN (RAILWAY)
         * Este bloque detecta si la aplicación está en la nube y fuerza
         * que todos los enlaces y formularios se envíen por HTTPS seguro.
         */
        if (config('app.env') === 'production' || config('app.env') === 'staging') {
            URL::forceScheme('https');
        }

        /**
         * 2. CONFIGURACIÓN DE PAGINACIÓN
         * Definimos que Laravel use el diseño de Bootstrap 5 para las listas.
         */
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');

        /**
         * 3. COMPOSER DE VISTAS (LÓGICA DE NEGOCIO)
         * Compartimos la información del horario del parqueo en todas las vistas.
         */
        View::composer('*', function (\Illuminate\View\View $view): void {
            $view->with([
                'parkingOpen' => ParkingHours::isOpenForIngresso(),
                'parkingHoursLabel' => ParkingHours::label(),
            ]);
        });
    }
}