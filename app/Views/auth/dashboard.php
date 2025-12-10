<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">

            <!-- Admin Dashboard -->
            <?php if ($role === 'admin'): ?>
                <div class="mb-4">
                    <h2 class="text-primary mb-4"><i class="bi bi-people-fill"></i> Admin Dashboard</h2>
                </div>

                <!-- Enrollment Management Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-journal-check"></i> Enrollment Management</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $db = \Config\Database::connect();
                                    $enrollmentModel = new \App\Models\EnrollmentModel();

                                    // Get enrollment stats
                                    $pendingCount = $db->table('enrollments')->where('status', 'pending')->countAllResults();
                                    $approvedCount = $db->table('enrollments')->where('status', 'approved')->countAllResults();
                                    $totalEnrollments = $db->table('enrollments')->countAllResults();
                                    $courseCount = $db->table('courses')->countAllResults();
                                    ?>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-warning"><?= $pendingCount ?></h3>
                                            <p class="mb-1">Pending Requests</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-success"><?= $approvedCount ?></h3>
                                            <p class="mb-1">Approved Enrollments</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-primary"><?= $totalEnrollments ?></h3>
                                            <p class="mb-1">Total Enrollments</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-info"><?= $courseCount ?></h3>
                                            <p class="mb-1">Available Courses</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 text-center">
                                        <a href="<?= base_url('index.php/enrollment/teacher') ?>" class="btn btn-primary me-2">
                                            <i class="bi bi-gear"></i> Manage Enrollments
                                        </a>
                                        <a href="<?= base_url('index.php/enrollment/force') ?>" class="btn btn-success">
                                            <i class="bi bi-person-plus"></i> Force Enroll Student
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h2 class="text-primary"><i class="bi bi-people-fill"></i> User Management</h2>
                    </div>
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
                <!-- Enrollment Management Section for Teachers -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Enrollment Management</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $enrollmentModel = new \App\Models\EnrollmentModel();
                        $teacher_id = session()->get('user_id');
                        $pendingRequests = $enrollmentModel->getPendingRequests($teacher_id);
                        $totalEnrollments = count($enrollmentModel->getEnrollmentsForTeacher($teacher_id));
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h3 class="text-warning"><?= count($pendingRequests) ?></h3>
                                    <p class="mb-1">Pending Requests</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h3 class="text-primary"><?= $totalEnrollments ?></h3>
                                    <p class="mb-1">Total Enrollments</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="<?= base_url('index.php/enrollment/teacher') ?>" class="btn btn-success me-2">
                                <i class="bi bi-gear"></i> Manage Requests
                            </a>
                            <a href="<?= base_url('index.php/enrollment/force') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus"></i> Force Enroll
                            </a>
                        </div>
                        <?php if (!empty($pendingRequests)): ?>
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle"></i> You have pending enrollment requests that need your approval.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

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

                <!-- Class Schedule -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <?php
                            // Filter approved/force_enrolled courses
                            $activeCourses = array_filter($enrolledCourses ?? [], function($enrollment) {
                                return in_array($enrollment['status'], ['approved', 'force_enrolled']);
                            });

                            // Collect unique academic periods
                            $academicPeriods = [];
                            foreach ($activeCourses as $course) {
                                $key = ($course['school_year'] ?? '') . '|' . ($course['semester'] ?? '');
                                if (!isset($academicPeriods[$key]) && ($course['school_year'] ?? '') && ($course['semester'] ?? '')) {
                                    $academicPeriods[$key] = [
                                        'school_year' => $course['school_year'],
                                        'semester' => $course['semester']
                                    ];
                                }
                            }

                            // Sort periods by school year desc, semester
                            usort($academicPeriods, function($a, $b) {
                                if ($a['school_year'] !== $b['school_year']) {
                                    return strcmp($b['school_year'], $a['school_year']);
                                }
                                $semOrder = ['Summer' => 1, '2nd' => 2, '1st' => 3]; // Latest semester first
                                return ($semOrder[$b['semester']] ?? 0) <=> ($semOrder[$a['semester']] ?? 0);
                            });

                        ?>
                        <h5 class="card-title"><i class="bi bi-calendar-event"></i> Class Schedule</h5>

                        <?php
                            if (!empty($activeCourses)):
                                // Group courses by academic period
                                $coursesByPeriod = [];
                                foreach ($activeCourses as $course) {
                                    // Skip courses without schedule data
                                    if (empty($course['schedule_days']) || empty($course['schedule_time_start']) || empty($course['schedule_time_end'])) {
                                        continue;
                                    }
                                    $periodKey = $course['school_year'] . '|' . $course['semester'];
                                    if (!isset($coursesByPeriod[$periodKey])) {
                                        $coursesByPeriod[$periodKey] = [];
                                    }
                                    $coursesByPeriod[$periodKey][] = $course;
                                }

                                // Sort periods by school year desc, semester
                                ksort($coursesByPeriod);
                                $coursesByPeriod = array_reverse($coursesByPeriod);

                                $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

                                foreach ($coursesByPeriod as $periodKey => $periodCourses):
                                    // Get period info from last course
                                    $periodInfo = explode('|', $periodKey);
                                    $schoolYear = $periodInfo[0];
                                    $semester = $periodInfo[1];
                        ?>
                                    <h6 class="mt-4 mb-3 fw-bold text-primary">
                                        <i class="bi bi-journal"></i> <?= esc($schoolYear) ?> - <?= esc($semester) ?> Semester
                                    </h6>

                                    <?php
                                        $scheduleData = [];
                                        foreach ($periodCourses as $course) {
                                            $days = array_map('trim', explode(',', strtolower($course['schedule_days'])));
                                            if ($days && !empty(array_filter($days))) {
                                                $start = date('g:i A', strtotime($course['schedule_time_start']));
                                                $end = date('g:i A', strtotime($course['schedule_time_end']));
                                                $timeSlot = "{$start} - {$end}";
                                                foreach ($days as $day) {
                                                    if (!empty($day)) {
                                                        $scheduleData[$day][] = [
                                                            'time' => $timeSlot,
                                                            'code' => $course['course_code'],
                                                            'title' => $course['course_name']
                                                        ];
                                                    }
                                                }
                                            }
                                        }

                                        // Collect all unique time slots for this period
                                        $allTimes = [];
                                        foreach ($scheduleData as $dayClasses) {
                                            foreach ($dayClasses as $class) {
                                                $allTimes[] = $class['time'];
                                            }
                                        }
                                        $allTimes = array_unique($allTimes);
                                        sort($allTimes);
                                    ?>

                                    <?php if (!empty($allTimes)): ?>
                                        <div class="table-responsive mb-4">
                                            <table class="table table-bordered">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th style="width: 120px;">Time</th>
                                                        <th>Course</th>
                                                        <?php foreach ($weekdays as $day): ?>
                                                            <th><?php echo ucfirst($day); ?></th>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        foreach ($allTimes as $time):
                                                            echo '<tr>';
                                                            echo '<td>' . esc($time) . '</td>';
                                                            echo '<td>';
                                                            // Show courses for this time slot
                                                            $timeCourses = [];
                                                            foreach ($weekdays as $day) {
                                                                if (isset($scheduleData[$day])) {
                                                                    $dayCourses = array_filter($scheduleData[$day], fn($c) => $c['time'] === $time);
                                                                    if (!empty($dayCourses)) {
                                                                        $timeCourses = array_merge($timeCourses, $dayCourses);
                                                                    }
                                                                }
                                                            }
                                                            if (!empty($timeCourses)) {
                                                                foreach ($timeCourses as $course) {
                                                                    echo '<div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">';
                                                                    echo '<div>';
                                                                    echo '<strong>' . esc($course['code']) . '</strong> - ' . esc($course['title']);
                                                                    echo '</div>';
                                                                    echo '<small class="text-muted">' . esc($course['days'] ?? 'TBA') . '</small>';
                                                                    echo '</div>';
                                                                }
                                                            }
                                                            echo '</td>';
                                                            foreach ($weekdays as $day):
                                                                echo '<td>';
                                                                if (isset($scheduleData[$day])) {
                                                                    $dayCourses = array_filter($scheduleData[$day], fn($c) => $c['time'] === $time);
                                                                    if (!empty($dayCourses)) {
                                                                        echo '<div class="text-center">';
                                                                        foreach ($dayCourses as $course) {
                                                                            echo '<span class="badge bg-primary mb-1">' . esc($course['code']) . '</span><br>';
                                                                        }
                                                                        echo '</div>';
                                                                    }
                                                                }
                                                                echo '</td>';
                                                            endforeach;
                                                            echo '</tr>';
                                                        endforeach;
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                        <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No active enrollments found.</p>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Enrollment Status Section -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-journal-check"></i> My Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $enrollmentModel = new \App\Models\EnrollmentModel();
                        $user_id = session()->get('user_id');
                        $allEnrollments = $enrollmentModel->getUserEnrollments($user_id);
                        $pendingCount = 0;
                        $approvedCount = 0;
                        $deniedCount = 0;
                        foreach ($allEnrollments as $enrollment) {
                            switch ($enrollment['status']) {
                                case 'pending': $pendingCount++; break;
                                case 'approved': case 'force_enrolled': $approvedCount++; break;
                                case 'denied': $deniedCount++; break;
                            }
                        }
                        ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-success"><?= $approvedCount ?></h4>
                                    <p class="mb-1">Active Enrollments</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-warning"><?= $pendingCount ?></h4>
                                    <p class="mb-1">Pending Approval</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4 class="text-danger"><?= $deniedCount ?></h4>
                                    <p class="mb-1">Rejected</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="<?= base_url('index.php/enrollment/student') ?>" class="btn btn-primary me-2">
                                <i class="bi bi-list-check"></i> View All Enrollments
                            </a>
                            <a href="<?= base_url('/courses') ?>" class="btn btn-outline-success">
                                <i class="bi bi-plus-circle"></i> Browse Courses
                            </a>
                        </div>
                        <?php if ($pendingCount > 0): ?>
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle"></i> You have pending enrollment requests waiting for teacher approval.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Enrolled Courses Section -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Current Enrolled Courses</h5>
                        <div id="enrolled-courses-container">
                            <?php if (empty($enrolledCourses ?? [])): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No courses enrolled yet.</p>
                                    <a href="<?= base_url('courses') ?>" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Browse Available Courses
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row" id="enrolled-row">
                                    <?php foreach ($enrolledCourses as $course): ?>
                                        <?php if (in_array($course['status'], ['approved', 'force_enrolled'])): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-success">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-1"><?= esc($course['course_name'] ?? $course['title'] ?? 'Unknown Course') ?></h6>
                                                        <span class="badge bg-success">Active</span>
                                                    </div>
                                                    <p class="card-text small text-muted mb-2">
                                                        <?= esc($course['description'] ?? 'No description available.') ?>
                                                    </p>
                                                    <div class="row text-center">
                                                        <div class="col-12 text-end">
                                                            <small class="text-muted me-2">Enrolled: <?= date('M j, Y', strtotime($course['enrolled_at'])) ?></small>
                                                            <a href="<?= base_url('index.php/student/materials') ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-book"></i> View Materials
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Removed Available Courses Section -->

            <?php endif; ?>


        </div>


    </div>




<?= $this->endSection() ?>
