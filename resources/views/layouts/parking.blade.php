<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · Cañoto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --park-bg: #f0f4f8;
            --park-surface: #ffffff;
            --park-ink: #0f172a;
            --park-muted: #64748b;
            --park-accent: #0369a1;
            --park-accent-soft: #e0f2fe;
            --park-sidebar: #0b1220;
            --park-sidebar-hover: rgba(255, 255, 255, 0.06);
            --park-line: rgba(148, 163, 184, 0.25);
            --park-radius: 0.75rem;
            --park-font: "IBM Plex Sans", system-ui, sans-serif;
            --park-display: "Outfit", var(--park-font);
        }
        body {
            font-family: var(--park-font);
            background: var(--park-bg);
            color: var(--park-ink);
            min-height: 100vh;
        }
        h1, h2, h3, h4, h5, h6, .park-display {
            font-family: var(--park-display);
            letter-spacing: -0.02em;
        }
        .navbar-park {
            background: var(--park-surface) !important;
            border-bottom: 1px solid var(--park-line);
            box-shadow: 0 1px 0 rgba(15, 23, 42, 0.04);
        }
        .navbar-park .navbar-brand {
            color: var(--park-ink) !important;
            font-weight: 700;
            font-family: var(--park-display);
        }
        .navbar-park .nav-link {
            color: var(--park-muted) !important;
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.35rem 0.75rem !important;
        }
        .navbar-park .nav-link:hover { color: var(--park-accent) !important; }
        .sidebar-park {
            background: var(--park-sidebar);
            min-height: calc(100vh - 57px);
        }
        .sidebar-park .nav-link {
            color: rgba(255, 255, 255, 0.72);
            border-radius: 0.5rem;
            margin: 0.125rem 0.65rem;
            padding: 0.55rem 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .sidebar-park .nav-link:hover {
            background: var(--park-sidebar-hover);
            color: #fff;
        }
        .sidebar-park .nav-link.active {
            background: linear-gradient(135deg, rgba(3, 105, 161, 0.35), rgba(14, 165, 233, 0.2));
            color: #fff;
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.25);
        }
        .sidebar-park .small-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255, 255, 255, 0.35);
            padding: 1rem 1.25rem 0.35rem;
        }
        .park-card {
            background: var(--park-surface);
            border: 1px solid var(--park-line);
            border-radius: var(--park-radius);
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.05);
        }
        .park-card .card-header {
            background: transparent;
            border-bottom: 1px solid var(--park-line);
            font-weight: 600;
            font-family: var(--park-display);
        }
        .park-badge-open {
            background: var(--park-accent-soft);
            color: var(--park-accent);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
        }
        .park-badge-closed {
            background: #fef3c7;
            color: #b45309;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
        }
        .table-park thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--park-muted);
            font-weight: 600;
            border-bottom-width: 1px;
        }
        .offcanvas-park-dark .offcanvas-header { background: var(--park-sidebar); color: #fff; }
        .offcanvas-park-dark .btn-close { filter: invert(1); }
        main.park-main { max-width: 1280px; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column">
    <nav class="navbar navbar-expand-lg navbar-light navbar-park sticky-top py-2">
        <div class="container-fluid px-3 px-lg-4">
            <button class="btn btn-light border d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarPark" aria-controls="sidebarPark">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('parking.ingreso') }}">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center text-white fw-bold"
                      style="width:2rem;height:2rem;background:linear-gradient(135deg,#0369a1,#0ea5e9);font-size:0.85rem;">C</span>
                Cañoto Parking
            </a>
            <div class="d-none d-lg-flex align-items-center gap-3 ms-auto">
                @if($parkingOpen ?? true)
                    <span class="park-badge-open"><i class="bi bi-check-circle-fill me-1"></i>Ingresos abiertos · {{ $parkingHoursLabel ?? '07:00 — 23:59' }}</span>
                @else
                    <span class="park-badge-closed"><i class="bi bi-moon-fill me-1"></i>Cerrado · Horario {{ $parkingHoursLabel ?? '07:00 — 23:59' }}</span>
                @endif
                <div class="vr text-secondary opacity-25 my-1"></div>
                <ul class="navbar-nav flex-row gap-1">
                    <li class="nav-item"><a class="nav-link" href="{{ route('parking.ingreso') }}">Ingreso</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('parking.salida') }}">Salida</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="offcanvas offcanvas-start offcanvas-park-dark d-md-none" tabindex="-1" id="sidebarPark">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title park-display">Menú</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body p-0 sidebar-park" style="min-height: 100%;">
            @include('partials.parking-sidebar-nav', ['mobile' => true])
        </div>
    </div>

    <div class="flex-grow-1 d-flex container-fluid px-0">
        <aside class="sidebar-park d-none d-md-flex flex-column py-3 px-0 flex-shrink-0" style="width: 15.5rem;">
            @include('partials.parking-sidebar-nav', ['mobile' => false])
        </aside>
        <main class="flex-grow-1 py-4 px-3 px-lg-4 park-main mx-auto w-100">
            @if (session('status'))
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert" style="border-radius: var(--park-radius);">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm" style="border-radius: var(--park-radius);">
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
