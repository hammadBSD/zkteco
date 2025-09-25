<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZKTeco Attendance Dashboard</title>
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
                        <a class="nav-link active" href="{{ route('attendance.index') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link" href="#" onclick="fetchTodayData()">
                            <i class="fas fa-calendar-day"></i> Today's Data
                        </a>
                        <a class="nav-link" href="#" onclick="fetchLimitedData(15)">
                            <i class="fas fa-list"></i> Recent Records
                        </a>
                        <a class="nav-link" href="{{ route('settings.index') }}">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-line"></i> Attendance Dashboard</h2>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $totalEmployees }}</h4>
                                        <p class="mb-0">Total Employees</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $todayCount }}</h4>
                                        <p class="mb-0">Today's Records</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-day fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>{{ $totalRecords }}</h4>
                                        <p class="mb-0">Total Records</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-database fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Fetching data from ZKTeco devices...</p>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer"></div>

                <!-- Attendance Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Today's Attendance Records</h5>
                    </div>
                    <div class="card-body">
                        @if($todayAttendances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Punch Code</th>
                                            <th>Name</th>
                                            <th>Device</th>
                                            <th>Punch Time</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todayAttendances as $attendance)
                                            <tr>
                                                <td>{{ $attendance->punch_code_id }}</td>
                                                <td>{{ $attendance->employee->name ?? 'Unknown' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->device_type == 'IN' ? 'success' : 'warning' }}">
                                                        {{ $attendance->device_type }}
                                                    </span>
                                                </td>
                                                <td>{{ $attendance->punch_time->format('H:i:s') }}</td>
                                                <td>{{ $attendance->verify_mode }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $attendance->is_processed ? 'success' : 'secondary' }}">
                                                        {{ $attendance->is_processed ? 'Processed' : 'Pending' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No attendance records for today</h5>
                                <p class="text-muted">Click "Fetch Today's Data" to retrieve attendance from ZKTeco devices</p>
                            </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHtml;
        }

        function fetchTodayData() {
            showLoading();
            showAlert('Fetching today\'s attendance data from ZKTeco devices...', 'info');
            
            fetch('/attendance/fetch-today', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Fetch error:', error);
                showAlert('Error: ' + error.message, 'danger');
            });
        }

        function fetchLimitedData(limit) {
            showLoading();
            showAlert(`Fetching ${limit} recent attendance records from ZKTeco devices...`, 'info');
            
            fetch(`/attendance/fetch-limited/${limit}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Fetch error:', error);
                showAlert('Error: ' + error.message, 'danger');
            });
        }
    </script>
            </div>
        </div>
    </div>
</body>
</html>
