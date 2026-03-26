# Cañoto Parking System (Laravel 12)

Sistema de gestión de estacionamiento de cinco pisos para Windows con XAMPP (PHP 8.2 + MySQL). Este módulo cubre **control de ingreso** (búsqueda, validación y asignación de piso) y **salida con facturación** según el tipo de cliente.

## Requisitos

- PHP 8.2+, Composer, MySQL (MariaDB), extensiones habituales de Laravel (`pdo_mysql`, etc.).

## Instalación rápida

1. Copie `.env.example` a `.env` y configure `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
2. `composer install`
3. `php artisan key:generate`
4. `php artisan migrate`
5. (Opcional) `php artisan db:seed` para datos de demostración.

Servidor de desarrollo: `php artisan serve`. Rutas principales:

- Ingreso: `GET /parking/ingreso`
- Búsqueda: `POST /parking/buscar`
- Registrar ingreso: `POST /parking/ingreso`
- Salida / ticket: `GET|POST /parking/salida`
- Clientes (CRUD): `GET /parking/clientes`, etc.
- Vehículos (CRUD): `GET /parking/vehiculos`, etc.

## Horario de operación (ingresos)

El **registro de ingreso** de vehículos solo se permite entre **07:00 y 23:59** (hora de `config('app.timezone')`). Fuera de ese rango se puede **buscar** en el panel, pero no confirmar entrada. La **salida** y el **ticket** pueden procesarse en cualquier momento (vehículos que siguen dentro). Lógica en `App\Support\ParkingHours` y validación en `ParkingController::store`.

## Tipos de cliente y tarifas

| Tipo        | Cuota        | Pisos        | Cobro por estadía        |
|------------|--------------|-------------|---------------------------|
| Visitante  | Bs. 5 / hora o fracción | 2 al 5 | Sí (al salir)             |
| Abonado    | Bs. 200 / mes | 2 al 5 | No (ticket Bs. 0)         |
| Abonado VIP| Bs. 400 / mes | **1** (prioridad) | No (ticket Bs. 0) |

Si un **abonado** o **VIP** tiene la fecha de **próximo pago vencida**, el sistema lo trata como **visitante** en ese ingreso: pisos 2–5 y cobro por tiempo al salir.

## Búsqueda y validación (ingreso)

- **Placa:** se normaliza (mayúsculas, sin espacios ni guiones) y se busca por **coincidencia exacta**. Formato válido: **6 a 8 caracteres alfanuméricos** (`A–Z`, `0–9`).
- **Nombre del cliente:** búsqueda **parcial** (`LIKE %texto%`) sobre el nombre del cliente asociado a cada vehículo.
- Debe indicarse **al menos uno** de los dos criterios.
- Si la placa es válida pero **no existe** en la base de datos, se muestra el **registro rápido** (nombre, marca, modelo, color y tipo de cliente) y al guardar se crea cliente + vehículo y se registra el ingreso.

### Visitante recurrente

Si el cliente ya tuvo al menos una estadía **cerrada** registrada previamente con **tipo efectivo visitante**, se muestra un **badge** sugiriendo ofrecer un plan de abonado.

## Jerarquía y asignación automática de pisos

La prioridad es la que define el **tipo efectivo** en el momento del ingreso (tras validar si el abono está al día):

1. **Abonado VIP** con pago vigente → **piso 1**.
2. **Abonado** con pago vigente → **pisos 2 a 5**.
3. **Visitante** (incluye visitante de registro y abonado vencido tratado como visitante) → **pisos 2 a 5**.

Dentro de los pisos **2–5**, el piso concreto se calcula de forma **determinista** con la placa (`crc32` módulo 4, más 2), para repartir de forma estable entre los cuatro pisos sin necesidad de tabla de espacios en el MVP.

## Facturación en salida (`TicketController`)

- **`tipo_efectivo` guardado en el ingreso** (no el tipo “de carnet”) es el que manda al salir.
- **Abonado** o **Abonado VIP** en ese campo → **total Bs. 0** (autorización sin cobro).
- **Visitante** → total = **horas cobradas × Bs. 5**. Las horas se obtienen con **fracción al alza**: cualquier minuto extra cuenta como hora adicional (mínimo 1 hora cobrada).

## Estructura de base de datos

- **`clientes`:** `nombre`, `tipo_cliente` (`visitante`, `abonado`, `abonado_vip`), `fecha_proximo_pago`, etc.
- **`vehiculos`:** `cliente_id`, `placa` (única), `marca`, `modelo`, `color`.
- **`ingresos`:** sesión de parqueo: `entrada_at`, `salida_at`, `piso`, `tipo_registrado`, `tipo_efectivo`, `abono_vencido_tratado_como_visitante`, `total_bs`.

## Catálogo (clientes y vehículos)

- **Clientes:** alta, edición y eliminación desde `/parking/clientes`. No se elimina un cliente si aún tiene vehículos vinculados.
- **Vehículos:** alta, edición y eliminación desde `/parking/vehiculos`. No se elimina si hay **ingreso activo** o **cualquier historial** de ingresos asociado al vehículo.

Controladores: `ClienteController`, `VehiculoController`. Normalización de placa: `App\Support\Placa`.

## Interfaz

**Bootstrap 5**, **Bootstrap Icons** y tipografías **Outfit** / **IBM Plex Sans** (Google Fonts). Layout en `layouts/parking.blade.php`: barra clara con estado de horario, **sidebar** oscuro en escritorio, offcanvas en móvil, tarjetas `park-card`, tablas compactas y paginación Bootstrap 5 (`Paginator::defaultView`).

## Archivos relevantes

- `routes/web.php` — rutas del módulo.
- `app/Http/Controllers/ParkingController.php` — `ingreso`, `search`, `store`.
- `app/Http/Controllers/TicketController.php` — `salida`, `procesarSalida`.
- `app/Http/Controllers/ClienteController.php`, `VehiculoController.php` — CRUD.
- `app/Support/ParkingHours.php`, `Placa.php`.
- `resources/views/parking/*`, `resources/views/catalog/*`.
