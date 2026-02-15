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
        <div class="d-flex w-100 min-vh-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <div class="d-flex flex-column flex-grow-1">
                @include('layout.header')
                <main class="pt-3 w-100 overflow-y-auto flex-grow-1">
                    <div class="container py-4">
                        <div class="row">
                            <div class="col-12">

                                <!-- Header -->
                                <div class="nofication-header d-flex justify-content-between align-items-center mb-4 flex-wrap">
                                    <h2><strong>📬</strong> Notifications</h2>
                                    <div class="d-flex flex-wrap ms-auto gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm mark-all-as-read-btn">
                                            ✓ Mark All as Read
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-all-notification-record">
                                            🗑️ Delete All Read
                                        </button>

                                        {{-- Only nurses can see the schedule settings button --}}
                                        @if(Auth::user()->role === 'nurse')
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#notificationScheduleModal">
                                            ⚙️ Notification Schedule
                                        </button>
                                        @endif
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
                                                            <button type="button"
                                                                class="btn btn-outline-success btn-sm notification-mark-as-read-btn"
                                                                data-notification-id="{{ $notification->id }}"
                                                                title="Mark as Read">
                                                                ✓
                                                            </button>
                                                            @endif
                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm notification-delete-btn"
                                                                data-notification-id="{{ $notification->id }}"
                                                                title="Delete">
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

                    {{-- ============================================================ --}}
                    {{-- NOTIFICATION SCHEDULE MODAL (nurse only)                     --}}
                    {{-- ============================================================ --}}
                    @if(Auth::user()->role === 'nurse')
                    <div class="modal fade" id="notificationScheduleModal" tabindex="-1"
                        aria-labelledby="notificationScheduleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="notificationScheduleModalLabel">
                                        ⚙️ Notification Schedule Settings
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <p class="text-muted small mb-3">
                                        Set the time each notification will be sent daily. Changes take effect on the next scheduled run.
                                    </p>

                                    {{-- Alert placeholder - managed entirely by JS, no Bootstrap dismiss --}}
                                    <div id="scheduleAlertBox" style="display:none;"></div>

                                    {{-- Schedule rows --}}
                                    @foreach($notificationSchedules as $schedule)
                                    <div class="card mb-3 border-0 bg-light rounded-3">
                                        <div class="card-body py-3 px-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <span class="fw-semibold">
                                                        @switch($schedule->label)
                                                        @case('appointment-reminder') 📅 Appointment Reminder Notification @break
                                                        @case('overdue-notifications') ⚠️ Overdue Appointment Notification @break
                                                        @case('staff-daily-schedule') 🗓️ Staff Daily Schedule Notification @break
                                                        @default {{ $schedule->label }}
                                                        @endswitch
                                                    </span>
                                                </div>

                                                {{-- Active toggle --}}
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input schedule-active-toggle"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="active_{{ $schedule->id }}"
                                                        data-id="{{ $schedule->id }}"
                                                        {{ $schedule->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label small text-muted"
                                                        for="active_{{ $schedule->id }}">
                                                        {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Time input --}}
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="small text-muted mb-0" style="min-width:80px;">Send time:</label>
                                                <input type="time"
                                                    class="form-control form-control-sm schedule-time-input"
                                                    id="time_{{ $schedule->id }}"
                                                    data-id="{{ $schedule->id }}"
                                                    value="{{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('H:i') }}">
                                                <button type="button"
                                                    class="btn btn-primary btn-sm schedule-save-btn"
                                                    data-id="{{ $schedule->id }}"
                                                    data-url="{{ route('notification-schedules.update', $schedule->id) }}">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- ============================================================ --}}

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

    {{-- Schedule modal JS (nurse only) --}}
    @if(Auth::user()->role === 'nurse')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const alertBox = document.getElementById('scheduleAlertBox');
            let alertTimer = null;

            function hideAlert() {
                if (alertTimer) {
                    clearTimeout(alertTimer);
                    alertTimer = null;
                }
                alertBox.style.display = 'none';
            }

            function showAlert(message, type = 'success') {
                if (alertTimer) {
                    clearTimeout(alertTimer);
                    alertTimer = null;
                }

                const bgColor = type === 'success' ? '#d1e7dd' : (type === 'danger' ? '#f8d7da' : '#fff3cd');
                const txtColor = type === 'success' ? '#0f5132' : (type === 'danger' ? '#842029' : '#664d03');

                alertBox.removeAttribute('class');
                alertBox.style.cssText = [
                    'display:flex',
                    'justify-content:space-between',
                    'align-items:center',
                    'padding:10px 14px',
                    'margin-bottom:12px',
                    'border-radius:6px',
                    'background-color:' + bgColor,
                    'color:' + txtColor,
                    'font-size:0.9rem'
                ].join(';');

                alertBox.innerHTML =
                    '<span>' + message + '</span>' +
                    '<button onclick="this.parentElement.style.display=\'none\'" ' +
                    'style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:' + txtColor + ';padding:0 0 0 12px;line-height:1;">' +
                    '&times;</button>';

                alertTimer = setTimeout(hideAlert, 4000);
            }

            // Update label on toggle switch
            document.querySelectorAll('.schedule-active-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    label.textContent = this.checked ? 'Active' : 'Inactive';
                });
            });

            // Save button click
            document.querySelectorAll('.schedule-save-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    const url = this.dataset.url;
                    const timeVal = document.getElementById(`time_${id}`).value;
                    const isActive = document.getElementById(`active_${id}`).checked;

                    if (!timeVal) {
                        showAlert('Please select a valid time.', 'warning');
                        return;
                    }

                    // Disable button while saving
                    this.disabled = true;
                    this.textContent = 'Saving...';

                    try {
                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                scheduled_time: timeVal,
                                is_active: isActive ? 1 : 0,
                            }),
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            showAlert(`✓ ${result.message}`, 'success');
                        } else {
                            const errors = result.errors ?
                                Object.values(result.errors).flat().join(' ') :
                                result.message ?? 'Something went wrong.';
                            showAlert(`✗ ${errors}`, 'danger');
                        }
                    } catch (error) {
                        showAlert('✗ Network error. Please try again.', 'danger');
                    } finally {
                        this.disabled = false;
                        this.textContent = 'Save';
                    }
                });
            });

        });
    </script>
    @endif

    @if(Auth::user()->role != 'patient')
    @if($isActive)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('notification');
            if (con) con.classList.add('active');
        });
    </script>
    @endif
    @else
    @if($isActive)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_notification');
            if (con) con.classList.add('active');
        });
    </script>
    @endif
    @endif

</body>

</html>