<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/notification/notification.js'])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100 min-vh-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class=" d-flex flex-column flex-grow-1 ">
                @include('layout.header')
                <main class="pt-3 w-100 overflow-y-auto flex-grow-1 ">
                    <div class="container py-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h2><strong>📬 Notifications</strong></h2>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mark-all-as-read-btn">
                                            ✓ Mark All as Read
                                        </button>

                                        <button type="button" class="btn btn-outline-danger btn-sm delete-all-notification-record">
                                            🗑️ Delete All Read
                                        </button>
                                    </div>
                                </div>

                                @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif

                                <!-- Unread Count -->
                                @if($unreadCount > 0)
                                <div class="alert alert-info">
                                    <strong>You have {{ $unreadCount }} unread {{ $unreadCount === 1 ? 'notification' : 'notifications' }}</strong>
                                </div>
                                @endif

                                <!-- Notifications List -->
                                <div class="card">
                                    <div class="card-body p-0">
                                        @forelse($notifications as $notification)
                                        <div class="notification-item {{ $notification->is_read ? '' : 'notification-unread' }} p-3 border-bottom">
                                            <div class="d-flex align-items-start">
                                                <!-- Icon -->
                                                <div class="notification-icon-large notification-{{ $notification->appointment_type }} me-3">
                                                    @switch($notification->appointment_type)
                                                    @case('vaccination') 💉 @break
                                                    @case('prenatal') 🤰 @break
                                                    @case('senior_citizen') 👴 @break
                                                    @case('tb_dots') 🏥 @break
                                                    @case('family_planning') 👨‍👩‍👧‍👦 @break
                                                    @default 📅
                                                    @endswitch
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h5 class="mb-1">
                                                                {{ $notification->title }}
                                                                @if(!$notification->is_read)
                                                                <span class="badge bg-primary">New</span>
                                                                @endif
                                                            </h5>
                                                            <p class="mb-2">{{ $notification->message }}</p>
                                                            <small class="text-muted">
                                                                <i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>

                                                        <!-- Actions -->
                                                        <div class="btn-group btn-group-sm">
                                                            @if(!$notification->is_read)
                                                            <button type="button" class="btn btn-outline-success btn-sm notification-mark-as-read-btn" data-notification-id="{{ $notification->id}}" title="Mark as Read">
                                                                ✓
                                                            </button>
                                                            @endif

                                                            <button type="button" class="btn btn-outline-danger btn-sm notification-delete-btn" data-notification-id="{{ $notification->id}}" title="Delete">
                                                                🗑️
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-5">
                                            <div class="mb-3" style="font-size: 4rem;">📭</div>
                                            <h5 class="text-muted">No notifications yet</h5>
                                            <p class="text-muted">We'll notify you when you have upcoming appointments</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .notification-item {
                            transition: background-color 0.2s;
                        }

                        .notification-item:hover {
                            background-color: #f8f9fa;
                        }

                        .notification-unread {
                            background-color: #e3f2fd;
                        }

                        .notification-icon-large {
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 30px;
                            flex-shrink: 0;
                        }

                        .notification-vaccination {
                            background-color: #e3f2fd;
                        }

                        .notification-prenatal {
                            background-color: #f3e5f5;
                        }

                        .notification-senior_citizen {
                            background-color: #fff3e0;
                        }

                        .notification-tb_dots {
                            background-color: #e8f5e9;
                        }

                        .notification-family_planning {
                            background-color: #fce4ec;
                        }
                    </style>


                </main>
            </div>
        </div>
    </div>
    @if(Auth::user()-> role !='patient')
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('notification');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
    @else
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_notification');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
    @endif
</body>

</html>