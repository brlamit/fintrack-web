<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinTrack Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .navbar-brand, .nav-link {
            color: #1e293b !important;
        }
        .nav-link:hover {
            color: #0ea5e9 !important;
        }
        .nav-link.active {
            color: #0ea5e9 !important;
            font-weight: 600;
        }
        .admin-card {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: white;
        }
        /* Loader */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }
        #page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Page Loader -->
    <div id="page-loader" class="hidden">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 fw-medium text-secondary">Loading...</p>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-chart-pie text-white" style="font-size: 0.8rem;"></i>
                </div>
                <span class="fw-bold tracking-tight">FinTrack Admin</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.groups.index') }}">Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.transactions') }}">Transactions</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->avatar)
                                <img id="navbar-avatar-img" 
                                     src="{{ auth()->user()->avatar }}?v={{ auth()->user()->updated_at?->timestamp ?? time() }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="rounded-circle" 
                                     width="36" height="36" 
                                     style="object-fit:cover;"
                                     crossorigin="anonymous"
                                     referrerpolicy="no-referrer"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/uploads/images/default.png') }}';">
                            @else
                                <i class="fas fa-user-shield"></i>
                            @endif
                            <span id="navbar-username">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-id-badge me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.security') }}">
                                    <i class="fas fa-lock me-2"></i>Security
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.preferences') }}">
                                    <i class="fas fa-cog me-2"></i>Preferences
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    {{-- Main content area --}}
    <div class="container mt-4">
       
     <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="flash-success">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="flash-error">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
   

        @yield('content')
    </div>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Flash messages
            document.querySelectorAll('#flash-success, #flash-error').forEach(alert => {
                setTimeout(() => {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }, 7000);
            });

            // Form submission loader
            document.querySelectorAll('form').forEach(form => {
                // Ignore the logout form if it's purely triggered by JS
                if (form.id === 'admin-logout-form') return;
                
                form.addEventListener('submit', () => {
                    const loader = document.getElementById('page-loader');
                    loader.classList.remove('hidden');
                });
            });
        });

        // Show loader on link click (optional, but good for UX)
        window.onbeforeunload = function() {
            document.getElementById('page-loader').classList.remove('hidden');
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
