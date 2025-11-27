<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?> ITE311-ALBARINA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: black;
    }

    .navbar {
      background-color: #222;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      color: #ffffff;
      letter-spacing: 1px;
    }

    .navbar .nav-link {
      color: #f8f9fa;
      font-size: 1.1rem;
      font-weight: 500;
      margin: 0 5px;
      transition: all 0.3s ease;
    }

    .navbar .nav-link:hover {
      color: #0d6efd;
      text-decoration: underline;
    }

    .container {
      margin-top: 40px;
      background: gray;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      color: white;
    }
  </style>
</head>
<body>
  <?php $session = session(); ?>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid"> <!-- Full width -->
      <a class="navbar-brand" href="<?= base_url('/') ?>">MyCI</a>

      <!-- Toggler for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/courses') ?>">Courses</a></li>
          <!-- Notifications Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-bell"></i>
              <span class="badge bg-danger" id="notification-badge" style="display: none;"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" id="notifications-list">
              <li><h6 class="dropdown-header">Notifications</h6></li>
              <!-- Notifications will be loaded here -->
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-center" href="#">View All</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/logout') ?>">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- jQuery for Notifications -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Function to load notifications
      function loadNotifications() {
        $.get('<?= base_url('notifications') ?>')
          .done(function(data) {
            // Update badge
            if (data.unread_count > 0) {
              $('#notification-badge').text(data.unread_count).show();
            } else {
              $('#notification-badge').hide();
            }

            // Clear existing notifications except header and footer
            $('#notifications-list li:not(.dropdown-header, .dropdown-divider, :last-child)').remove();

            // Add notifications
            if (data.notifications.length > 0) {
              data.notifications.forEach(function(notification) {
                var readClass = notification.is_read == 1 ? 'text-muted' : '';
                var itemHtml = `
                  <li>
                    <div class="dropdown-item ${readClass}" data-id="${notification.id}">
                      <small>${notification.message}</small>
                      ${notification.is_read == 0 ? '<button class="btn btn-sm btn-outline-primary ms-2 mark-read-btn">Mark Read</button>' : ''}
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
        var notificationId = $(this).parent().data('id');
        $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId)
          .done(function(response) {
            if (response.success) {
              loadNotifications(); // Reload notifications
            }
          })
          .fail(function() {
            alert('Failed to mark as read');
          });
      });

      // Optional: Refresh notifications every 60 seconds
      setInterval(loadNotifications, 60000);
    });
  </script>
</body>
</html>
