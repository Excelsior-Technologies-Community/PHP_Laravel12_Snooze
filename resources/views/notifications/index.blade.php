<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Reminder System</title>
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
        .notification-unread {
            background-color: #f0f7ff;
            border-left: 4px solid #667eea;
        }
        .notification-read {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h4 class="mb-0" style="color: #667eea;">
                            <i class="bi bi-bell-fill"></i> 
                            Notifications
                        </h4>
                        <div>
                            <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-outline-success me-2">
                                <i class="bi bi-check-all"></i> Mark All Read
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrow-left"></i> Back to Reminders
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="mb-3">
                            <span class="badge bg-primary">Unread: {{ Auth::user()->unreadNotifications->count() }}</span>
                            <span class="badge bg-secondary">Total: {{ Auth::user()->notifications->count() }}</span>
                        </div>

                        @forelse($notifications as $notification)
                            <div class="card mb-2 {{ $notification->read_at ? 'notification-read' : 'notification-unread' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title">
                                                @if(!$notification->read_at)
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                                <i class="bi bi-bell"></i>
                                                {{ $notification->data['title'] ?? 'Reminder Due' }}
                                            </h6>
                                            <p class="card-text text-muted">
                                                {{ $notification->data['message'] ?? 'Your reminder is due!' }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> 
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="btn-group">
                                            @if(!$notification->read_at)
                                                <a href="{{ route('notifications.mark-read', $notification->id) }}" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="Mark as read">
                                                    <i class="bi bi-check"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('notifications.delete', $notification->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this notification?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-bell-slash" style="font-size: 3rem; color: #ddd;"></i>
                                <p class="mt-2 text-muted">No notifications yet.</p>
                            </div>
                        @endforelse

                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>