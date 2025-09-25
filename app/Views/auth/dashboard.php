<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

    <!-- ✅ Page Content -->
    <div class="flex-grow-1">
        <!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topNavbar">
        <!-- Left side (Welcome + Role) -->
        <ul class="navbar-nav me-auto d-flex align-items-center">
            <li class="nav-item me-3 text-white">
                Welcome, <strong><?= esc($user_name) ?></strong>
            </li>
            <li class="nav-item me-3 text-muted">
                Role: <strong><?= esc($role) ?></strong>
            </li>
        </ul>

        <!-- Right side (Logout button only) -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>


        <!-- Main Content -->
        <div class="container-fluid mt-4">

            <!-- Admin Dashboard -->
            <?php if ($role === 'admin'): ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text fs-3"><?= esc($roleData['total_users'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Recently Registered Users</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($roleData['recent_users'])): ?>
                                            <?php foreach ($roleData['recent_users'] as $user): ?>
                                                <tr>
                                                    <td><?= esc($user['name']) ?></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="2">No recent users found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Teacher Dashboard -->
            <?php if ($role === 'teacher'): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">My Students</h5>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($roleData['students'])): ?>
                                    <?php foreach ($roleData['students'] as $student): ?>
                                        <tr>
                                            <td><?= esc($student['name']) ?></td>
                                            <td><?= esc($student['email']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2">No students assigned yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Student Dashboard -->
            <?php if ($role === 'student'): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">My Profile</h5>
                        <p><strong>Name:</strong> <?= esc($roleData['profile']['name'] ?? '') ?></p>
                        <p><strong>Email:</strong> <?= esc($roleData['profile']['email'] ?? '') ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
