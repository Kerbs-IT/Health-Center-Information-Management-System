<header class="d-flex align-items-center pe-3 w-100 position-sticky top-0">
  <nav class="d-flex justify-content-between align-items-center w-100">
    <div class="box d-flex gap-3 align-items-center justify-content-center">
      <button class="btn hamburger d-lg-block fs-6 mx-1" id="toggleSidebar">
        <i class="fa-solid fa-bars fs-2"></i>
      </button>
      @if ($page === 'DASHBOARD')
      <h1 class="mb-0">Welcome, <span>{{ Auth::user()->username ?? 'Guest' }}</span></h1>
      @else
      <h1 class="mb-0">{{ $page }}</h1>
      @endif
    </div>
    <div class="right-info d-flex align-items-center justify-content-center gap-3 z-1">

      <!-- UPDATED: Notification Bell with Dynamic Badge -->
      <button type="button" class="btn position-relative p-0 border-0 bg-transparent" id="notificationBell" data-bs-toggle="modal" data-bs-target="#notificationModal">
        <!-- Bell SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
          class="bi bi-bell" viewBox="0 0 16 16">
          <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z" />
          <path d="M8 1a4 4 0 0 0-4 4c0 1.098-.354 2.5-.975 3.5-.356.596-.525 1.057-.525 1.5h11c0-.443-.169-.904-.525-1.5C12.354 7.5 12 6.098 12 5a4 4 0 0 0-4-4z" />
        </svg>

        <!-- Dynamic Badge - Hidden if no unread notifications -->
        <span id="notificationBadge" style="
          position: absolute;
          top: 2px;
          right: 2px;
          min-width: 18px;
          height: 18px;
          background-color: red;
          border-radius: 50%;
          border: 2px solid white;
          color: white;
          font-size: 10px;
          font-weight: bold;
          display: none;
          align-items: center;
          justify-content: center;
          padding: 0 4px;">
        </span>
      </button>

      @php
      $profileImage = null;

      if (optional(Auth::user()->nurses)->profile_image) {
      $profileImage = asset(Auth::user()->nurses->profile_image);
      } elseif (optional(Auth::user()->staff)->profile_image) {
      $profileImage = asset(Auth::user()->staff->profile_image);
      } elseif (optional(Auth::user())->profile_image) {
      $profileImage = asset(Auth::user()->profile_image);
      } elseif (optional(Auth::user()->patient)->profile_image) {
      $profileImage = asset(Auth::user()->patient->profile_image);
      } else {
      $profileImage = asset('images/profile_images/default_profile.png');
      }
      @endphp

      <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
        <img src="{{ $profileImage }}" alt="profile picture" class="profile-img z-1" id="profile_img">
        <div class="username-n-role">
          <h5 class="mb-0">
            {{
              optional(Auth::user()->nurses)->full_name
              ?? optional(Auth::user()->staff)->full_name
              ?? optional(Auth::user()->patient)->full_name 
              ?? (function() {
                  $user = Auth::user();
                  $middle = $user->middle_initial ? strtoupper(substr($user->middle_initial, 0, 1)) . '.' : '';
                  return ucwords(trim(implode(' ', array_filter([$user->first_name, $middle, $user->last_name]))));
              })()
              ?? 'none'
            }}
          </h5>
          <h6 class="mb-0 text-muted fw-light">{{Auth::user()->role ?? 'none'}}</h6>
        </div>
        <div class="links position-absolute z-index flex-column top-17 w-100 bg-white" id="links" style="z-index: 9999;">
          <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
          <a href="{{route('logout')}}" class="text-decoration-none text-black" id="headerLogOut">Logout</a>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- UPDATED: Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mx-1" id="notificationModalLabel">
          <i class="fas fa-bell"></i> Notifications
        </h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
          <i class="fas fa-check-double"></i> Mark all as read
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
        <!-- Loading State -->
        <div id="notificationsLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 text-muted">Loading notifications...</p>
        </div>

        <!-- Notifications List -->
        <div id="notificationsList" style="display: none;">
          <!-- Notifications will be dynamically loaded here -->
        </div>

        <!-- Empty State -->
        <div id="notificationsEmpty" style="display: none;" class="text-center py-5">
          <div style="font-size: 4rem;">📭</div>
          <h5 class="mt-3 text-muted">No notifications yet</h5>
          <p class="text-muted">We'll notify you when you have upcoming appointments</p>
        </div>
      </div>
      <div class="modal-footer">
        <a href="/notifications" class="btn btn-primary w-100">
          <i class="fas fa-list"></i> View All Notifications
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Notification Styles -->
<style>
  .notification-item {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s;
    cursor: pointer;
  }

  .notification-item:hover {
    background-color: #f8f9fa;
  }

  .notification-item:last-child {
    border-bottom: none;
  }

  .notification-unread {
    background-color: #e3f2fd;
    border-left: 4px solid #2196F3;
  }

  .notification-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
  }

  .notification-icon-vaccination {
    background-color: #e3f2fd;
  }

  .notification-icon-prenatal {
    background-color: #f3e5f5;
  }

  .notification-icon-senior_citizen {
    background-color: #fff3e0;
  }

  .notification-icon-tb_dots {
    background-color: #e8f5e9;
  }

  .notification-icon-family_planning {
    background-color: #fce4ec;
  }

  .notification-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 5px;
    color: #333;
  }

  .notification-message {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 5px;
    line-height: 1.4;
  }

  .notification-time {
    font-size: 0.8rem;
    color: #999;
  }

  .notification-badge-new {
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 12px;
  }
</style>

<!-- Notification JavaScript -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Load unread count on page load
    loadUnreadCount();

    // Refresh unread count every 60 seconds
    setInterval(loadUnreadCount, 60000);

    // Load notifications when modal is opened
    const notificationModal = document.getElementById('notificationModal');
    notificationModal.addEventListener('show.bs.modal', function() {
      loadNotifications();
    });

    // Mark all as read button
    document.getElementById('markAllReadBtn').addEventListener('click', function() {
      markAllNotificationsAsRead();
    });
  });

  /**
   * Load unread notification count
   */
  function loadUnreadCount() {
    fetch('/notifications/unread-count')
      .then(response => response.json())
      .then(data => {
        const badge = document.getElementById('notificationBadge');
        if (data.count > 0) {
          badge.textContent = data.count > 99 ? '99+' : data.count;
          badge.style.display = 'flex';
        } else {
          badge.style.display = 'none';
        }
      })
      .catch(error => console.error('Error loading notification count:', error));
  }

  /**
   * Load notifications list
   */
  function loadNotifications() {
    const loadingDiv = document.getElementById('notificationsLoading');
    const listDiv = document.getElementById('notificationsList');
    const emptyDiv = document.getElementById('notificationsEmpty');

    // Show loading
    loadingDiv.style.display = 'block';
    listDiv.style.display = 'none';
    emptyDiv.style.display = 'none';

    fetch('/notifications/recent')
      .then(response => response.json())
      .then(data => {
        loadingDiv.style.display = 'none';

        if (data.length === 0) {
          emptyDiv.style.display = 'block';
          return;
        }

        // Build notifications HTML
        let html = '';
        data.forEach(notification => {
          const icon = getNotificationIcon(notification.appointment_type);
          const unreadClass = !notification.is_read ? 'notification-unread' : '';
          const timeAgo = getTimeAgo(notification.created_at);
          const badgeHtml = !notification.is_read ?
            '<span class="badge bg-primary notification-badge-new ms-2">New</span>' :
            '';

          html += `
          <div class="notification-item ${unreadClass}" onclick="handleNotificationClick(${notification.id}, '${notification.link_url || ''}')">
            <div class="d-flex align-items-start">
              <div class="notification-icon notification-icon-${notification.appointment_type} me-3">
                ${icon}
              </div>
              <div class="flex-grow-1">
                <div class="notification-title">
                  ${notification.title}
                  ${badgeHtml}
                </div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">
                  <i class="far fa-clock"></i> ${timeAgo}
                </div>
              </div>
            </div>
          </div>
        `;
        });

        listDiv.innerHTML = html;
        listDiv.style.display = 'block';
      })
      .catch(error => {
        console.error('Error loading notifications:', error);
        loadingDiv.style.display = 'none';
        emptyDiv.innerHTML = `
        <div class="text-center py-4">
          <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
          <p class="mt-3 text-danger">Failed to load notifications</p>
        </div>
      `;
        emptyDiv.style.display = 'block';
      });
  }

  /**
   * Handle notification click - mark as read
   */
  function handleNotificationClick(notificationId, linkUrl) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(() => {
        loadUnreadCount();
        loadNotifications();

        // Navigate to link if provided
        if (linkUrl && linkUrl !== 'null' && linkUrl !== '') {
          window.location.href = linkUrl;
        }
      })
      .catch(error => console.error('Error marking notification as read:', error));
  }
  /**
   * Mark all notifications as read
   */
  function markAllNotificationsAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(() => {
        loadUnreadCount();
        loadNotifications();
      })
      .catch(error => console.error('Error marking all as read:', error));
  }

  /**
   * Get notification icon based on appointment type
   */
  function getNotificationIcon(type) {
    const icons = {
      'vaccination': '💉',
      'prenatal': '🤰',
      'senior_citizen': '👴',
      'tb_dots': '🏥',
      'family_planning': '👨‍👩‍👧‍👦'
    };
    return icons[type] || '📅';
  }

  /**
   * Calculate time ago
   */
  function getTimeAgo(timestamp) {
    const now = new Date();
    const past = new Date(timestamp);
    const diffInSeconds = Math.floor((now - past) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    return past.toLocaleDateString();
  }
</script>