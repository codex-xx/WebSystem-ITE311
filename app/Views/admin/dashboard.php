<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
<body class="dark">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="alert alert-success">
            Welcome, <strong><?= session()->get('username') ?></strong>!  
            You are logged in as <span class="badge bg-primary">Admin</span>.
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Add, update, or remove users in the system.</p>
                        <a href="#" class="btn btn-dark">Go</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text">View system logs and reports.</p>
                        <a href="#" class="btn btn-dark">View</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
