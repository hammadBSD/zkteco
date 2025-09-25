<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZKTeco Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            padding: 0;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 12px 20px;
            border: none;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            padding: 30px;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h5 class="text-white mb-4">
                        <i class="fas fa-cogs"></i> ZKTeco Dashboard
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="{{ route('attendance.index') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link" href="#" onclick="fetchTodayData()">
                            <i class="fas fa-calendar-day"></i> Today's Data
                        </a>
                        <a class="nav-link" href="#" onclick="fetchLimitedData(15)">
                            <i class="fas fa-list"></i> Recent Records
                        </a>
                        <a class="nav-link active" href="{{ route('settings.index') }}">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-cog"></i> Settings</h2>
                </div>

                <!-- Settings Content -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs"></i> System Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Settings Panel</strong><br>
                                    Configure your ZKTeco system settings here. More options will be available based on your requirements.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-dark">
                                            <div class="card-header bg-dark text-white">
                                                <h6 class="mb-0"><i class="fas fa-server"></i> Device Configuration</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Configure ZKTeco device connections and settings.</p>
                                                <button class="btn btn-dark btn-sm" disabled>
                                                    <i class="fas fa-cog"></i> Configure Devices
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-dark">
                                            <div class="card-header bg-dark text-white">
                                                <h6 class="mb-0"><i class="fas fa-sync"></i> Sync Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Manage data synchronization with HR system.</p>
                                                <button class="btn btn-dark btn-sm" disabled>
                                                    <i class="fas fa-sync"></i> Sync Configuration
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card border-dark">
                                            <div class="card-header bg-dark text-white">
                                                <h6 class="mb-0"><i class="fas fa-clock"></i> Schedule Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Configure automatic data fetching schedules.</p>
                                                <button class="btn btn-dark btn-sm" disabled>
                                                    <i class="fas fa-clock"></i> Schedule Settings
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card border-dark">
                                            <div class="card-header bg-dark text-white">
                                                <h6 class="mb-0"><i class="fas fa-shield-alt"></i> Security Settings</h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted">Manage API keys and security configurations.</p>
                                                <button class="btn btn-dark btn-sm" disabled>
                                                    <i class="fas fa-shield-alt"></i> Security Settings
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Functions from attendance page for consistency
        function fetchTodayData() {
            // Redirect to attendance page and trigger fetch
            window.location.href = "{{ route('attendance.index') }}";
        }
        
        function fetchLimitedData(limit) {
            // Redirect to attendance page and trigger fetch
            window.location.href = "{{ route('attendance.index') }}";
        }
    </script>
</body>
</html>
