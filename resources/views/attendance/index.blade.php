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
                        <a class="nav-link" href="#" onclick="syncAttendanceWithWebsite()">
                            <i class="fas fa-sync-alt"></i> Sync Attendance with Website
                        </a>
                        <a class="nav-link" href="#" onclick="showMonthlyAttendance()">
                            <i class="fas fa-calendar-alt"></i> Monthly Attendance
                        </a>
                        <a class="nav-link" href="#" onclick="showCronLogs()">
                            <i class="fas fa-tasks"></i> Cron Logs
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

        function syncAttendanceWithWebsite() {
            showLoading();
            showAlert('Syncing attendance data with website...', 'info');
            
            fetch('/attendance/sync-with-website', {
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
                    let message = data.message + '<br>';
                    if (data.data && data.data.summary) {
                        data.data.summary.forEach(line => {
                            message += line + '<br>';
                        });
                    }
                    showAlert(message, 'success');
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Sync error:', error);
                showAlert('Error: ' + error.message, 'danger');
            });
        }

        function showMonthlyAttendance() {
            showLoading();
            showAlert('Loading monthly attendance data...', 'info');
            
            fetch('/attendance/monthly-attendance', {
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
                    displayMonthlyAttendance(data.data);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Monthly attendance error:', error);
                showAlert('Error: ' + error.message, 'danger');
            });
        }

        function displayMonthlyAttendance(data) {
            let html = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Monthly Attendance - ${data.current_month}</h5>
                        <span class="badge bg-primary">Total: ${data.total_this_month} records</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Daily Summary</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Records Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            `;
            
            data.monthly_summary.forEach(day => {
                html += `
                    <tr>
                        <td>${new Date(day.date).toLocaleDateString()}</td>
                        <td><span class="badge bg-info">${day.total_records}</span></td>
                    </tr>
                `;
            });
            
            html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Recent Days Detail (Last 7 Days)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Punch Code</th>
                                                <th>Device</th>
                                                <th>Time</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            `;
            
            Object.keys(data.recent_days).forEach(date => {
                data.recent_days[date].forEach(record => {
                    html += `
                        <tr>
                            <td>${new Date(date).toLocaleDateString()}</td>
                            <td>${record.punch_code}</td>
                            <td><span class="badge bg-${record.device_type == 'IN' ? 'success' : 'warning'}">${record.device_type}</span></td>
                            <td>${record.time}</td>
                            <td>${record.punch_type || '-'}</td>
                        </tr>
                    `;
                });
            });
            
            html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Replace the main content with monthly attendance data
            document.querySelector('.main-content').innerHTML = html;
            showAlert('Monthly attendance data loaded successfully', 'success');
        }

        function showCronLogs() {
            showLoading();
            showAlert('Loading cron logs...', 'info');
            
            fetch('/attendance/cron-logs', {
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
                    displayCronLogs(data.data);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Cron logs error:', error);
                showAlert('Error: ' + error.message, 'danger');
            });
        }

        function displayCronLogs(data) {
            let html = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cron Job Logs</h5>
                        <div>
                            <span class="badge bg-success me-2">Success: ${data.statistics.successful_runs}</span>
                            <span class="badge bg-danger me-2">Failed: ${data.statistics.failed_runs}</span>
                            <span class="badge bg-warning">Running: ${data.statistics.running_jobs}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>${data.statistics.total_runs}</h4>
                                        <small>Total Runs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>${data.statistics.today_runs}</h4>
                                        <small>Today's Runs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4>${data.statistics.last_24h_runs}</h4>
                                        <small>Last 24h Runs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>${data.statistics.running_jobs}</h4>
                                        <small>Currently Running</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Recent Log Entries</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Started</th>
                                                <th>Month</th>
                                                <th>Status</th>
                                                <th>Duration</th>
                                                <th>Completed</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
            `;
            
            data.recent_logs.forEach(log => {
                const statusBadge = log.status === 'success' ? 'success' : 
                                  log.status === 'failed' ? 'danger' : 'warning';
                const statusText = log.status.charAt(0).toUpperCase() + log.status.slice(1);
                
                html += `
                    <tr>
                        <td>${new Date(log.started_at).toLocaleString()}</td>
                        <td>${log.month || '-'}</td>
                        <td><span class="badge bg-${statusBadge}">${statusText}</span></td>
                        <td>${log.duration_seconds ? (Math.floor(log.duration_seconds/60) + 'm ' + (log.duration_seconds%60) + 's') : '-'}</td>
                        <td>${log.completed_at ? new Date(log.completed_at).toLocaleString() : '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(${log.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Replace the main content with cron logs data
            document.querySelector('.main-content').innerHTML = html;
            showAlert('Cron logs loaded successfully', 'success');
        }

        function viewLogDetails(logId) {
            // This could be expanded to show detailed log information
            showAlert(`Viewing details for log ID: ${logId}`, 'info');
        }
    </script>
            </div>
        </div>
    </div>
</body>
</html>
