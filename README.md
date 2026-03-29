# Canoto Parking System

## Descripción
Canoto Parking System es un sistema completo de gestión de estacionamiento para un edificio de 5 pisos, desarrollado en Laravel 12. Permite el control de **ingreso** de vehículos (búsqueda por placa o nombre, validación, registro rápido si nuevo, asignación automática de piso según tipo de cliente), **salida con facturación** (cálculo de horas con fracción al alza para visitantes, gratis para abonados vigentes) y **CRUD** de clientes y vehículos.

**Características clave**:
- Tipos de cliente: Visitante (Bs. 5/hora), Abonado (Bs. 200/mes, pisos 2-5), Abonado VIP (Bs. 400/mes, piso 1 prioritario).
- Horario de ingreso: 07:00-23:59.
- Asignación de piso determinista basada en placa para pisos 2-5.
- Validación y normalización de placas (6-8 alfanuméricos).
- Soporte para clientes recurrentes (sugerencia de abono).
- Demo data via seeder.

## Tecnologías
- **Backend**: PHP 8.2+, Laravel 12 (framework MVC), Composer.
- **Base de datos**: MySQL 5.7 (Docker).
- **Frontend**: Tailwind CSS 4, Vite 7, Bootstrap 5, Blade templates.
- **Herramientas**: Artisan, PHPUnit (tests), Docker Compose (app, db, phpMyAdmin).
- **Dependencias clave**: laravel/framework ^12.0, laravel/sail, tailwindcss ^4.0.

## Instalación
### Opción 1: Desarrollo local (XAMPP/WAMP)
1. Copia `.env.example` a `.env` y configura DB (`DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=canoto_parking`, `DB_USERNAME=root`, `DB_PASSWORD=`).
2. `composer install`
3. `php artisan key:generate`
4. `npm install && npm run build`
5. `php artisan migrate`
6. (Opcional) `php artisan db:seed --class=CanotoParkingDemoSeeder` para datos demo.
7. `php artisan serve` (accede a http://localhost:8000).

### Opción 2: Docker (recomendado)
1. `docker-compose up -d --build`
2. Ejecuta migraciones: `docker-compose exec app php artisan migrate`
3. Seed demo: `docker-compose exec app php artisan db:seed --class=CanotoParkingDemoSeeder`
4. Accede a:
   - App: http://localhost:8000
   - phpMyAdmin: http://localhost:8080 (user: root, pass: root, DB: Canoto-Parking)

Script completo local: `composer setup` (genera .env, key, migrate, npm).

## Uso
- **Ingreso vehículo**: GET/POST `/parking/ingreso` (buscar placa/nombre, registrar).
- **Salida/Ticket**: GET/POST `/parking/salida` (procesar pago por tiempo).
- **Catálogo clientes**: `/parking/clientes` (CRUD).
- **Catálogo vehículos**: `/parking/vehiculos` (CRUD).
- Inicio redirige a ingreso.

**Arquitectura principal**:
- **Controllers**: `ParkingController` (ingreso), `TicketController` (salida), `ClienteController`, `VehiculoController`.
- **Models**: `Cliente`, `Vehiculo`, `Ingreso`.
- **Services/Support**: `ParkingSpotService`, `ParkingHours`, `Placa`.
- **Vistas**: `resources/views/parking/` (operaciones), `catalog/` (CRUD), `layouts/parking.blade.php`.
- **DB**: Tablas `clientes`, `vehiculos`, `ingresos` (con soft deletes).

Para desarrollo: `npm run dev` (hot reload). Tests: `php artisan test`.
