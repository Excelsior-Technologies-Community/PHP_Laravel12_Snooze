<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Snooze Reminder System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .table th {
            border-top: none;
            color: #667eea;
        }
        .badge-reached {
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .badge-waiting {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .btn-snooze {
            background: #ffc107;
            color: #000;
            border: none;
            transition: all 0.3s;
        }
        .btn-snooze:hover {
            background: #e0a800;
            transform: scale(1.05);
        }
        .snooze-options {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .reminder-row:hover .snooze-options {
            display: block;
        }
        .time-remaining {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Add notification bell at the top -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4" style="margin-top: -20px;">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-bell-fill text-warning"></i> 
                Reminder System
            </a>
            <div class="ms-auto">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-danger" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="notificationList" style="width: 300px;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center text-muted py-2">No new notifications</li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-transparent">
                        <h2 class="text-center mb-0" style="color: #667eea;">
                            <i class="bi bi-bell-fill"></i> 
                            Smart Reminder System with Snooze
                        </h2>
                    </div>
                    <div class="card-body">
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Create Reminder Form -->
                        <div class="card mb-4" style="background: #f8f9fa;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-plus-circle-fill" style="color: #667eea;"></i>
                                    Create New Reminder
                                </h5>
                                <form method="POST" action="{{ route('reminders.store') }}" class="row g-3">
                                    @csrf
                                    <div class="col-md-4">
                                        <input type="text" name="title" class="form-control" 
                                               placeholder="Reminder Title" required>
                                    </div>
                                    <div class="col-md-4">
                                        <textarea name="message" class="form-control" 
                                                  placeholder="Reminder Message" rows="1" required></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="datetime-local" name="remind_at" class="form-control" 
                                               required min="{{ now()->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Reminders Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Remind At</th>
                                        <th>Time Left</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reminders as $reminder)
                                    <tr class="reminder-row">
                                        <td>
                                            <strong>{{ $reminder->title }}</strong>
                                            @if($reminder->is_snoozed)
                                                <span class="badge bg-warning text-dark ms-2">
                                                    <i class="bi bi-moon"></i> Snoozed
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($reminder->message, 30) }}</td>
                                        <td>{{ $reminder->remind_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @if($reminder->isOverdue())
                                                <span class="text-danger">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> Overdue
                                                </span>
                                            @else
                                                <span class="time-remaining">
                                                    <i class="bi bi-clock"></i> 
                                                    {{ $reminder->time_remaining }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reminder->isOverdue())
                                                <span class="badge-reached">
                                                    <i class="bi bi-bell"></i> Time Reached
                                                </span>
                                            @else
                                                <span class="badge-waiting">
                                                    <i class="bi bi-hourglass-split"></i> Waiting
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(!$reminder->isOverdue())
                                                    <button type="button" class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#snoozeModal{{ $reminder->id }}">
                                                        <i class="bi bi-moon-stars-fill"></i> Snooze
                                                    </button>
                                                @else
                                                    <form action="{{ route('reminders.complete', $reminder->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="bi bi-check-lg"></i> Complete
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('reminders.delete', $reminder->id) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Delete this reminder?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Snooze Modal -->
                                            <div class="modal fade" id="snoozeModal{{ $reminder->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-moon-stars-fill text-warning"></i>
                                                                Snooze Reminder
                                                            </h5>
                                                            <button type="button" class="btn-close" 
                                                                    data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('reminders.snooze-custom', $reminder->id) }}" 
                                                              method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p><strong>{{ $reminder->title }}</strong></p>
                                                                <p class="text-muted">Current time: {{ $reminder->remind_at->format('h:i A') }}</p>
                                                                
                                                                <label class="form-label">Snooze for:</label>
                                                                <div class="row g-2">
                                                                    <div class="col-md-8">
                                                                        <input type="number" name="minutes" 
                                                                               class="form-control" 
                                                                               min="1" max="60" value="5" required>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <span class="form-control-plaintext">minutes</span>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <small class="text-muted">
                                                                        Quick snooze: 
                                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                                onclick="this.form.minutes.value=5">5m</button>
                                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                                onclick="this.form.minutes.value=10">10m</button>
                                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                                onclick="this.form.minutes.value=15">15m</button>
                                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                                onclick="this.form.minutes.value=30">30m</button>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" 
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="bi bi-moon-stars-fill"></i> 
                                                                    Snooze Reminder
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox-fill" style="font-size: 3rem; color: #ddd;"></i>
                                            <p class="mt-2 text-muted">No reminders yet. Create your first reminder!</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Reminders</h5>
                                        <h2>{{ $reminders->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-success">
                                    <div class="card-body">
                                        <h5 class="card-title">Waiting</h5>
                                        <h2>{{ $reminders->filter(function($r) { return !$r->isOverdue(); })->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-white bg-danger">
                                    <div class="card-body">
                                        <h5 class="card-title">Time Reached</h5>
                                        <h2>{{ $reminders->filter(function($r) { return $r->isOverdue(); })->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto-refresh every minute to update time remaining -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 60000); // Refresh every minute
    </script>
</body>
<!-- Simple Notification System -->
<!-- SweetAlert2 and Simple Auto Notification -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Store shown notifications to avoid duplicates
    let shownNotifications = new Set();

    // Function to check for newly due reminders
    function checkNewDueReminders() {
        // Get all rows with "Time Reached" status
        const dueRows = document.querySelectorAll('.badge-reached');
        
        dueRows.forEach(row => {
            const reminderRow = row.closest('tr');
            if (reminderRow) {
                // Get reminder details
                const title = reminderRow.querySelector('td:first-child strong')?.innerText || 'Reminder';
                const message = reminderRow.querySelector('td:nth-child(2)')?.innerText || 'Time is up!';
                const reminderId = title + message; // Simple unique identifier
                
                // Check if we haven't shown notification for this reminder yet
                if (!shownNotifications.has(reminderId)) {
                    // Add to shown set
                    shownNotifications.add(reminderId);
                    
                    // Show SweetAlert notification
                    Swal.fire({
                        title: '⏰ Time\'s Up!',
                        html: `<strong>${title}</strong><br>${message}`,
                        icon: 'warning',
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Snooze',
                        showDenyButton: true,
                        denyButtonText: 'Complete',
                        background: '#f8f9fa',
                        backdrop: `
                            rgba(0,0,0,0.4)
                            left top
                            no-repeat
                        `
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Find and click the snooze button
                            const snoozeBtn = reminderRow.querySelector('.btn-warning');
                            if (snoozeBtn) {
                                snoozeBtn.click();
                            }
                        } else if (result.isDenied) {
                            // Find and click the complete button
                            const completeBtn = reminderRow.querySelector('.btn-success');
                            if (completeBtn) {
                                completeBtn.click();
                            }
                        }
                    });
                }
            }
        });
    }

    // Check for new due reminders every 5 seconds
    checkNewDueReminders(); // Check immediately
    setInterval(checkNewDueReminders, 5000); // Then every 5 seconds
</script>
</html>