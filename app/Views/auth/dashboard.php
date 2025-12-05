<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">

            <!-- Admin Dashboard -->
            <?php if ($role === 'admin'): ?>
                <div class="mb-4">
                    <h2 class="text-primary mb-4"><i class="bi bi-people-fill"></i> User Management</h2>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text fs-3"><?= esc($roleData['total_users'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Students</h5>
                                <p class="card-text fs-3"><?= esc($roleData['total_students'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Teachers</h5>
                                <p class="card-text fs-3"><?= esc($roleData['total_teachers'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Admins</h5>
                                <p class="card-text fs-3"><?= esc($roleData['total_admins'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
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
                        <?php if (!empty($roleData['students'])): ?>
                            <div class="list-group">
                                <?php foreach ($roleData['students'] as $student): ?>
                                    <a href="<?= base_url('/teacher/student/' . $student['id']) ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= esc($student['name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($student['email']) ?></small>
                                            </div>
                                            <i class="bi bi-chevron-right"></i>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No students assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Teacher Courses Section -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Courses</h5>
                        <div class="list-group">
                            <?php
                            // Get all courses for teachers to view
                            $db = \Config\Database::connect();
                            $courses = $db->table('courses')->orderBy('title', 'ASC')->get()->getResultArray();
                            if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <div class="list-group-item">
                                        <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                        <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="list-group-item text-muted">No courses available.</div>
                            <?php endif; ?>
                        </div>
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

                <!-- Enrolled Courses Section -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Enrolled Courses</h5>
                        <div id="enrolled-courses-container">
                            <?php if (empty($enrolledCourses ?? [])): ?>
                                <p class="text-muted" id="enrolled-empty-msg">No courses enrolled yet.</p>
                            <?php else: ?>
                                <div class="row" id="enrolled-row">
                                    <?php foreach ($enrolledCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['course_name'] ?? $course['title'] ?? 'Unknown Course') ?></h6>
                                            <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                            <p class="text-muted small">Enrolled on: <?= date('M j, Y', strtotime($course['enrolled_at'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endif; ?>


        </div>


    </div>




<?= $this->endSection() ?>
