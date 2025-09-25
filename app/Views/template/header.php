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
    <div class="container-fluid">
      <span class="navbar-brand d-flex align-items-center">
        <?php if ($session->get('isLoggedIn')): ?>
          <span class="ms-3 text-white small">
            Hello, <?= esc($session->get('user_name')) ?> (<?= ucfirst(esc($session->get('role'))) ?>)
          </span>
        <?php endif; ?>
      </span>

      <!-- Mobile toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Nav items -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <?php if ($session->get('isLoggedIn')): ?>
              <?php if ($session->get('role') === 'admin'): ?>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/users') ?>">Manage Users</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/reports') ?>">Reports</a></li>
              <?php elseif ($session->get('role') === 'teacher'): ?>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/classes') ?>">My Classes</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/students') ?>">Students</a></li>
              <?php elseif ($session->get('role') === 'student'): ?>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('student/courses') ?>">My Courses</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('student/grades') ?>">My Grades</a></li>
              <?php endif; ?>
          <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="<?= base_url('/login') ?>">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="<?= base_url('/register') ?>">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
