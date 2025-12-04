<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">

            <!-- Admin Dashboard -->
            <?php if ($role === 'admin'): ?>
                <div class="mb-4">
                    <h2 class="text-primary mb-4"><i class="bi bi-people-fill"></i> User Management</h2>
                </div>

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

                <!-- Step 4: Enrolled Courses Section (Bootstrap Cards) -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Enrolled Courses</h5>
                        <div id="enrolled-courses-container">
                            <?php if (empty($enrolledCourses ?? [])): ?>
                                <p class="text-muted" id="enrolled-empty-msg">No courses enrolled yet. Check out available courses below!</p>
                            <?php else: ?>
                                <div class="row" id="enrolled-row">
                                    <?php foreach ($enrolledCourses as $course): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= esc($course['course_name'] ?? $course['title'] ?? 'Unknown Course') ?></h6>
                                            <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                            <p class="text-muted small">Enrolled on: <?= date('M j, Y', strtotime($course['enrolled_at'])) ?></p>
                                            <!-- Materials Section -->
                                            <?php
                                            $materialModel = new \App\Models\MaterialModel();
                                            $materials = $materialModel->getMaterialsByCourse($course['course_id']);
                                            if (!empty($materials)): ?>
                                                <h6 class="mt-3">Materials:</h6>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($materials as $material): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <span><?= esc($material['file_name']) ?></span>
                                                            <a href="<?= base_url('/materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-download"></i> Download
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted small mt-2">No materials available yet.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Available Courses Section (Bootstrap List Group) -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Available Courses</h5>
                        <div id="available-alert-container"></div> <!-- For dynamic alerts -->
                        <div id="available-courses-container">
                            <?php if (empty($availableCourses ?? [])): ?>
                                <p class="text-muted" id="available-empty-msg">Great! You're enrolled in all available courses.</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush" id="available-list">
                                    <?php foreach ($availableCourses as $course): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-top">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= esc($course['title'] ?? 'Unknown Course') ?></h6>
                                        <p class="mb-1 text-muted"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                    </div>
                                    <button class="btn btn-primary btn-sm enroll-btn"
                                            data-course-id="<?= esc($course['id']) ?>"
                                            data-course-name="<?= esc($course['title'] ?? 'Unknown Course') ?>"
                                            data-course-desc="<?= esc($course['description'] ?? 'No description available.') ?>"
                                            data-csrf="<?= csrf_hash() ?>">Enroll</button>
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

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--jQuery Script for AJAX Enrollment -->
    <?php if ($role === 'student'): ?>
    <script>
        $(document).ready(function() {
            $('.enroll-btn').click(function(e) {
                e.preventDefault();

                var button = $(this);
                var courseId = button.data('course-id');
                var courseName = button.data('course-name');
                var courseDesc = button.data('course-desc');
                var csrfToken = button.data('csrf');

                // Disable button during request
                button.prop('disabled', true).text('Enrolling...');

                // Clear any previous alerts
                $('#available-alert-container').empty();

                $.post('<?= base_url('course/enroll') ?>', {
                    course_id: courseId,
                    '<?= csrf_token() ?>': csrfToken  // CSRF field name (from Config/Security.php)
                })
                .done(function(response) {
                    if (response.success) {
                        // Step 5: Display Bootstrap Success Alert
                        var alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('#available-alert-container').html(alertHtml);

                        // Step 5: Hide/Remove the Enroll Button and Course Item
                        var listItem = button.closest('.list-group-item');
                        listItem.fadeOut(500, function() {
                            $(this).remove();
                            // Check if available list is now empty
                            if ($('#available-list .list-group-item').length === 0) {
                                $('#available-courses-container').html('<p class="text-muted" id="available-empty-msg">Great! You\'re enrolled in all available courses.</p>');
                            }
                        });

                        // Step 5: Dynamically Update Enrolled Courses List
                        var now = new Date();
                        var formattedDate = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        var newCardHtml = `
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">${courseName}</h6>
                                        <p class="card-text">${courseDesc}</p>
                                        <p class="text-muted small">Enrolled on: ${formattedDate}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        // If enrolled was empty, replace empty message with row
                        if ($('#enrolled-empty-msg').length > 0) {
                            $('#enrolled-courses-container').html('<div class="row" id="enrolled-row">' + newCardHtml + '</div>');
                        } else {
                            // Append to existing row
                            $('#enrolled-row').append(newCardHtml);
                        }
                    } else {
                        // Step 5: Display Bootstrap Error Alert
                        var alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> Error: ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('#available-alert-container').html(alertHtml);
                        button.prop('disabled', false).text('Enroll');
                    }
                })
                .fail(function() {
                    // Step 5: Display Bootstrap Error Alert on Failure
                    var alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> Enrollment failed. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    $('#available-alert-container').html(alertHtml);
                    button.prop('disabled', false).text('Enroll');
                });
            });
        });
    </script>
    <?php endif; ?>
<?= $this->endSection() ?>
