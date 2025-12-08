<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?> ITE311-ALBARINA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Layout wrapper */
    .wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar styles */
    .sidebar {
      width: 250px;
      background-color: #343a40;
      color: white;
      padding: 1rem;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      overflow: hidden;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar .sidebar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #495057;
    }

    .sidebar .sidebar-brand.collapsed {
      font-size: 1.2rem;
    }

    .sidebar .nav-item {
      margin-bottom: 0.5rem;
    }

    .sidebar .nav-link {
      color: #cbd3da;
      text-decoration: none;
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
      border-radius: 5px;
      background: linear-gradient(135deg, #495057, #5a6268);
      transition: all 0.3s ease;
      border: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .sidebar .nav-link:hover {
      background: linear-gradient(135deg, #5a6268, #6c757d);
      color: white;
      transform: translateX(5px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .sidebar .nav-link.active {
      background: linear-gradient(135deg, #6c757d, #7d8286);
      color: white;
      border-color: rgba(255,255,255,0.2);
    }

    .sidebar .nav-link i {
      margin-right: 0.5rem;
      font-size: 1.1rem;
    }

    /* Logout button positioning and styling */
    .sidebar .logout-container {
      position: absolute;
      bottom: 1rem;
      left: 1rem;
      right: 1rem;
    }

    .sidebar .logout-container .nav-link {
      background: linear-gradient(135deg, #dc3545, #c82333);
      border: 2px solid rgba(220, 53, 69, 0.3);
      border-radius: 10px;
      color: #fff !important;
      font-weight: bold;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
      box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
      transition: all 0.3s ease !important;
    }

    .sidebar .logout-container .nav-link:hover {
      background: linear-gradient(135deg, #c82333, #a71e2a);
      border-color: #c82333;
      color: #fff !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
    }

    .sidebar .logout-container .nav-link i {
      font-size: 1.2rem;
      filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.5));
    }

    .sidebar.collapsed .logout-container {
      left: 0.5rem;
      right: 0.5rem;
    }

    .sidebar.collapsed .logout-container .nav-link {
      justify-content: center;
      padding: 0.75rem 0.5rem;
    }

    /* Top bar styles */
    .topbar {
      height: 60px;
      background-color: #343a40;
      border-bottom: 1px solid #495057;
      padding: 0 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      z-index: 999;
      transition: all 0.3s ease;
    }

    .topbar.collapsed {
      left: 70px;
    }

    .topbar .user-info {
      display: flex;
      align-items: center;
    }

    .topbar .user-info .welcome-text {
      margin-right: 1rem;
      font-weight: 500;
      color: #e9ecef;
    }

    .topbar .user-info .role-badge {
      background-color: #6c757d;
      color: #ffffff;
      padding: 0.25rem 0.5rem;
      border-radius: 15px;
      font-size: 0.8rem;
      font-weight: 600;
      margin-right: 1rem;
    }

    .topbar .actions {
      display: flex;
      align-items: center;
    }

    .topbar .notifications {
      position: relative;
      margin-right: 1rem;
    }

    .topbar .notifications .btn {
      background: none;
      border: none;
      color: #ffffff;
      position: relative;
      padding: 0.5rem;
      margin-right: 0.5rem;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .topbar .notifications .btn:hover {
      color: #f8f9fa;
      transform: scale(1.1);
    }

    .topbar .notifications .badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: #dc3545;
      color: white;
      border-radius: 50%;
      padding: 3px 6px;
      font-size: 0.75rem;
      font-weight: bold;
      border: 2px solid white;
      box-shadow: 0 1px 3px rgba(0,0,0,0.2);
      min-width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Notifications dropdown styling to match profile dropdown */
    .topbar .notifications .dropdown-menu {
      min-width: 360px;
      padding: 0.75rem 0;
      border: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #notifications-list .dropdown-header,
    #notifications-list .dropdown-divider,
    #notifications-list .dropdown-item {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    #notifications-list .dropdown-item {
      white-space: normal;
    }

    /* Notification dropdown buttons */
    .topbar .notifications .dropdown-menu .mark-read-btn {
      color: #000 !important;
      border-color: #000 !important;
      background-color: #fff !important;
      font-weight: 600;
      padding: 0.25rem 0.65rem;
    }

    .topbar .notifications .dropdown-menu .mark-read-btn:hover,
    .topbar .notifications .dropdown-menu .mark-read-btn:focus {
      color: #fff !important;
      background-color: #000 !important;
      border-color: #000 !important;
    }

    .topbar .notifications .dropdown-menu .mark-read-btn:disabled {
      color: #6c757d !important;
      background-color: #f8f9fa !important;
      border-color: #ced4da !important;
    }

    .topbar .profile-dropdown .dropdown-toggle {
      background: none;
      border: none;
      color: #ffffff;
      position: relative;
      padding: 0.5rem;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.2s ease;
      border-radius: 0.375rem;
    }

    .topbar .profile-dropdown .dropdown-toggle:hover {
      color: #f8f9fa;
      background: rgba(255, 255, 255, 0.1);
      transform: scale(1.1);
    }

    .topbar .profile-dropdown .dropdown-toggle::after {
      margin-left: 0.3rem;
      font-size: 0.8rem;
    }

    .topbar .profile-dropdown .dropdown-menu {
      right: 0;
      left: auto;
      min-width: 200px;
      border: 1px solid rgba(0, 0, 0, 0.15);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Main content styles */
    .main-content {
      flex: 1;
      margin-left: 250px;
      margin-top: 60px;
      padding: 2rem;
      transition: all 0.3s ease;
    }

    .main-content.collapsed {
      margin-left: 70px;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .topbar {
        left: 0;
      }

      .topbar.collapsed {
        left: 0;
      }

      .main-content {
        margin-left: 0;
      }

      .main-content.collapsed {
        margin-left: 0;
      }
    }

    /* Toggle button */
    .sidebar-toggle {
      background: none;
      border: none;
      color: #e9ecef;
      padding: 0.5rem;
      margin-right: 1rem;
    }

    .sidebar-toggle:hover {
      color: #ffffff;
    }

    .sidebar-toggle i {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>
  <?php
  $session = session();
  $isLoggedIn = $session->get('isLoggedIn');
  $userRole = $session->get('role');
  $userName = $session->get('user_name');

  // Get current URI for active sidebar highlight
  $currentURI = current_url();
  $uri = service('uri');
  $currentSegment = $uri->getSegment(1);
  ?>

  <?php if ($isLoggedIn): ?>
  <div class="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <i class="bi bi-building"></i>
        ITE311
      </div>

      <div class="d-flex flex-column" style="height: 100%;">
        <ul class="nav flex-column flex-grow-1">
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'dashboard' || !$currentSegment ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
              <i class="bi bi-house-door"></i>
              <span>Dashboard</span>
            </a>
          </li>



          <!-- Admin specific navigation -->
          <?php if ($userRole === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'manage_course' ? 'active' : '' ?>" href="<?= base_url('manage_course') ?>">
              <i class="bi bi-gear"></i>
              <span>Manage Courses</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'user' ? 'active' : '' ?>" href="<?= base_url('user') ?>">
              <i class="bi bi-people"></i>
              <span>Users</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- Teacher specific navigation -->
          <?php if ($userRole === 'teacher'): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'courses' ? 'active' : '' ?>" href="<?= base_url('/courses') ?>">
              <i class="bi bi-book"></i>
              <span>Courses</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $uri->getSegment(2) === 'students' ? 'active' : '' ?>" href="<?= base_url('/teacher/students') ?>">
              <i class="bi bi-people"></i>
              <span>My Students</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'manage_course' ? 'active' : '' ?>" href="<?= base_url('manage_course') ?>">
              <i class="bi bi-upload"></i>
              <span>Manage Courses</span>
            </a>
          </li>
          <?php endif; ?>

          <!-- Student specific navigation -->
          <?php if ($userRole === 'student'): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentSegment === 'courses' ? 'active' : '' ?>" href="<?= base_url('/courses') ?>">
              <i class="bi bi-book"></i>
              <span>Courses</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($currentSegment === 'student' && $uri->getSegment(2) === 'materials') ? 'active' : '' ?>" href="<?= base_url('/student/materials') ?>">
              <i class="bi bi-cloud-download"></i>
              <span>My Materials</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($currentSegment === 'student' && $uri->getSegment(2) === 'grades') ? 'active' : '' ?>" href="<?= base_url('/student/grades') ?>">
              <i class="bi bi-journal-check"></i>
              <span>My Grades</span>
            </a>
          </li>
          <?php endif; ?>
        </ul>

        <div class="logout-container">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Top bar -->
    <header class="topbar">
      <div class="user-info">
        <button class="sidebar-toggle" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
        <span class="welcome-text">Welcome, <strong><?= esc($userName) ?></strong></span>
        <span class="role-badge"><?= ucfirst(esc($userRole)) ?></span>
      </div>

      <div class="actions">
        <!-- Notifications -->
        <div class="notifications dropdown">
          <button class="btn position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
            🔔
            <span class="badge position-absolute d-none" id="notification-badge"></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" id="notifications-list">
            <li><h6 class="dropdown-header">Notifications</h6></li>
            <!-- Notifications will be loaded here -->
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-center" href="#">View All</a></li>
          </ul>
        </div>

        <!-- Profile Dropdown -->
        <div class="profile-dropdown dropdown">
          <button class="dropdown-toggle position-relative" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Profile">
            👤
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li><h6 class="dropdown-header"><?= esc($userName) ?></h6></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </div>

      </div>
    </header>

    <!-- Main content -->
    <main class="main-content">
      <?= $this->renderSection('content') ?>
    </main>
  </div>
  <?php else: ?>
  <!-- Page Content for non-logged-in users -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- jQuery for Notifications -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Format date/time coming from the API
      function formatDate(dateString) {
        if (!dateString) return '';
        const parsed = new Date(dateString.replace(' ', 'T'));
        return isNaN(parsed) ? dateString : parsed.toLocaleString();
      }

      // Function to load notifications
      function loadNotifications() {
        $.getJSON('<?= base_url('notifications') ?>')
          .done(function(data) {
            // Update badge
            if (data.unread_count > 0) {
              $('#notification-badge').text(data.unread_count).removeClass('d-none');
            } else {
              $('#notification-badge').addClass('d-none');
            }

            // Clear existing notifications except header and footer
            $('#notifications-list li:not(.dropdown-header, .dropdown-divider, :last-child)').remove();

            // Add notifications
            if (data.notifications.length > 0) {
              data.notifications.forEach(function(notification) {
                var readClass = notification.is_read == 1 ? 'text-muted' : '';
                var buttonLabel = notification.is_read == 1 ? 'Read' : 'Mark Read';
                var buttonState = notification.is_read == 1 ? 'disabled' : '';
                var itemHtml = `
                  <li>
                    <div class="dropdown-item ${readClass}" data-id="${notification.id}">
                      <div class="d-flex justify-content-between align-items-start">
                        <small>${notification.message}</small>
                        <button class="btn btn-sm btn-outline-dark ms-2 mark-read-btn" ${buttonState}>${buttonLabel}</button>
                      </div>
                      ${notification.created_at ? `<div class="small text-muted mt-1">Received: ${formatDate(notification.created_at)}</div>` : ''}
                      ${notification.is_read == 1 ? `<div class="small text-success mt-1">Marked read</div>` : ''}
                    </div>
                  </li>
                `;
                $('#notifications-list li:last').before(itemHtml);
              });
            } else {
              $('#notifications-list li:last').before('<li><div class="dropdown-item text-muted">No notifications</div></li>');
            }
          })
          .fail(function() {
            console.log('Failed to load notifications');
          });
      }

      // Load notifications on page load
      loadNotifications();

      // Mark as read
      $(document).on('click', '.mark-read-btn', function(e) {
        e.stopPropagation();
        var $button = $(this);
        var $item = $button.closest('.dropdown-item');
        var notificationId = $item.data('id');
        if ($button.is(':disabled')) {
          return;
        }
        $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId)
          .done(function(response) {
            if (response.success) {
              // Show the time it was marked read immediately
              var readTime = new Date().toLocaleString();
              $item.addClass('text-muted');
              $button.text('Read').prop('disabled', true);
              $item.append(`<div class="small text-success mt-1">Marked read at ${readTime}</div>`);
              loadNotifications(); // Reload notifications and badge counts
            }
          })
          .fail(function() {
            alert('Failed to mark as read');
          });
      });

      // Optional: Refresh notifications every 60 seconds
      setInterval(loadNotifications, 60000);

      // Sidebar toggle functionality
      $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('.topbar').toggleClass('collapsed');
        $('.main-content').toggleClass('collapsed');
      });

      // Close sidebar on mobile when clicking outside
      $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
          if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
            $('#sidebar').removeClass('show');
          }
        }
      });
    });
  </script>
</body>
</html>
