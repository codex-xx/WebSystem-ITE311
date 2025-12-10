<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Enrollment Management</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <a href="/enrollment/force" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Force Enroll Student
                        </a>
                    </div>

                    <!-- Pending Requests Section -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="bi bi-clock"></i> Pending Enrollment Requests</h6>

                        <?php if (empty($pendingRequests)): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> No pending enrollment requests at this time.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Requested</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingRequests as $request): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($request['student_name']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($request['student_email']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($request['course_title']) ?></strong><br>
                                                    <small class="text-muted">Code: <?= esc($request['course_code']) ?></small>
                                                </td>
                                                <td>
                                                    <?= date('M d, Y H:i', strtotime($request['enrolled_at'])) ?>
                                                </td>
                                                <td>
                                                    <button onclick="approveRequest(<?= $request['id'] ?>)" class="btn btn-sm btn-success me-2">
                                                        <i class="bi bi-check"></i> Approve
                                                    </button>
                                                    <button onclick="denyRequest(<?= $request['id'] ?>)" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x"></i> Deny
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- All Enrollments Section -->
                    <div>
                        <h6 class="text-primary mb-3"><i class="bi bi-journal-check"></i> All Enrollments in Your Courses</h6>

                        <?php if (empty($allEnrollments)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No enrollments found in your courses.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Status</th>
                                            <th>Enrolled</th>
                                            <th>Processed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allEnrollments as $enrollment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($enrollment['student_name']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($enrollment['student_email']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($enrollment['course_title']) ?></strong><br>
                                                    <small class="text-muted">Code: <?= esc($enrollment['course_code']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge <?php
                                                        switch ($enrollment['status']) {
                                                            case 'approved': echo 'bg-success'; break;
                                                            case 'pending': echo 'bg-warning'; break;
                                                            case 'denied': echo 'bg-danger'; break;
                                                            case 'force_enrolled': echo 'bg-info'; break;
                                                            default: echo 'bg-secondary';
                                                        }
                                                    ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $enrollment['status'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= date('M d, Y H:i', strtotime($enrollment['enrolled_at'])) ?>
                                                </td>
                                                <td>
                                                    <?php if ($enrollment['processed_at']): ?>
                                                        <?= date('M d, Y H:i', strtotime($enrollment['processed_at'])) ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function approveRequest(enrollmentId) {
    if (confirm('Are you sure you want to approve this enrollment request?')) {
        fetch('<?= base_url("index.php/enrollment/approve") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'enrollment_id=' + enrollmentId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Enrollment approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the request.');
        });
    }
}

function denyRequest(enrollmentId) {
    if (confirm('Are you sure you want to deny this enrollment request?')) {
        fetch('<?= base_url("index.php/enrollment/deny") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'enrollment_id=' + enrollmentId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Enrollment denied successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the request.');
        });
    }
}
</script>
<?= $this->endSection() ?>
