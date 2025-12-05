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
      margin: 0;
      padding: 0;
    }

    /* Navbar Styles - Matching Dashboard Design */
    .navbar {
      background-color: #343a40;
      border-bottom: 1px solid #495057;
      padding: 1rem 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      color: #ffffff !important;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
    }

    .navbar-brand i {
      margin-right: 0.5rem;
      color: #ffffff;
    }

    .navbar .navbar-toggler {
      border: none;
      color: #ffffff;
    }

    .navbar .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.55%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    .navbar .nav-link {
      color: #e9ecef !important;
      font-size: 1.1rem;
      font-weight: 500;
      margin: 0 8px;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      transition: all 0.3s ease;
      background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
      border: 1px solid rgba(255,255,255,0.1);
    }

    .navbar .nav-link:hover {
      background: linear-gradient(135deg, #5a6268, #6c757d);
      color: #ffffff !important;
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .navbar .nav-link.active {
      background: linear-gradient(135deg, #6c757d, #7d8286);
      color: #ffffff !important;
      border-color: rgba(255,255,255,0.2);
    }

    /* Main Container Styles - Full Height Layout */
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .main-container {
      flex: 1;
      margin-top: 80px; /* Increased top margin to avoid navbar overlap */
    }

    .main-content {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      flex: 1;
    }

    .footer {
      margin-top: auto;
    }

    /* Welcome Section Styling */
    .welcome-section {
      background: linear-gradient(135deg, #343a40, #495057);
      color: white;
      padding: 3rem 2rem;
      border-radius: 15px;
      margin-bottom: 2rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      text-align: center;
    }

    .welcome-section h1 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 1rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .welcome-section p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      opacity: 0.9;
    }

    /* Content Cards Styling */
    .content-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border: 1px solid rgba(0,0,0,0.05);
      margin-bottom: 2rem;
      transition: all 0.3s ease;
    }

    .content-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .content-card h2, .content-card h3 {
      color: #343a40;
      font-weight: 600;
      margin-bottom: 1rem;
      border-bottom: 2px solid #0d6efd;
      padding-bottom: 0.5rem;
    }

    .content-card p {
      color: #6c757d;
      line-height: 1.7;
      font-size: 1.05rem;
    }

    /* Login Form Special Styling */
    .login-card {
      background: white;
      border-radius: 20px;
      padding: 3rem 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      border: 2px solid #e9ecef;
      max-width: 500px;
      margin: 0 auto;
    }

    .login-card h1 {
      color: #343a40;
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 700;
      font-size: 2.5rem;
    }

    .form-control {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
      background: linear-gradient(135deg, #0d6efd, #0056b3);
      border: none;
      border-radius: 10px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #0056b3, #004085);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
    }

    /* Alert Styling Matching Dashboard */
    .alert {
      border-radius: 10px;
      border: none;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .alert-success {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
    }

    .alert-danger {
      background: linear-gradient(135deg, #f8d7da, #f5c6cb);
      color: #721c24;
    }

    /* Footer Styling */
    .footer {
      background-color: #343a40;
      color: #e9ecef;
      text-align: center;
      padding: 1rem;
      margin-top: 3rem;
      border-radius: 15px 15px 0 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .welcome-section {
        padding: 2rem 1rem;
      }

      .welcome-section h1 {
        font-size: 2rem;
      }

      .content-card {
        padding: 1.5rem;
      }

      .login-card {
        padding: 2rem 1.5rem;
        margin: 1rem;
      }

      .navbar .nav-link {
        margin: 2px 4px;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
      }
    }

    /* Animation Effects */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .content-card {
      animation: fadeInUp 0.6s ease-out;
    }

    /* Academic/Student Friendly Colors */
    .text-primary { color: #0d6efd !important; }
    .bg-primary { background: linear-gradient(135deg, #0d6efd, #0056b3) !important; }
    .bg-secondary { background: linear-gradient(135deg, #6c757d, #5a6268) !important; }
    .bg-success { background: linear-gradient(135deg, #28a745, #1e7e34) !important; }
    .bg-info { background: linear-gradient(135deg, #17a2b8, #117a8b) !important; }
    .bg-warning { background: linear-gradient(135deg, #ffc107, #e0a800) !important; }
    .bg-danger { background: linear-gradient(135deg, #dc3545, #c82333) !important; }
  </style>
</head>
<body>
  <?php $session = session(); ?>

  <!-- Professional Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?= base_url('/') ?>">
        <i class="bi bi-building"></i>
        ITE311-ALBARINA
      </a>

      <!-- Mobile Toggler -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?= uri_string() === '' ? 'active' : '' ?>" href="<?= base_url('/') ?>">
              <i class="bi bi-house-door me-1"></i>Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= uri_string() === 'about' ? 'active' : '' ?>" href="<?= base_url('/about') ?>">
              <i class="bi bi-info-circle me-1"></i>About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= uri_string() === 'contact' ? 'active' : '' ?>" href="<?= base_url('/contact') ?>">
              <i class="bi bi-envelope me-1"></i>Contact
            </a>
          </li>
          <?php if ($session->get('isLoggedIn')): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/dashboard') ?>">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= uri_string() === 'login' ? 'active' : '' ?>" href="<?= base_url('/login') ?>">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content Area -->
  <div class="main-container">
    <div class="main-content">
      <?= $this->renderSection('content') ?>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
