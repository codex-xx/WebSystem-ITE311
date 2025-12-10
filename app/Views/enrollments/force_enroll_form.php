<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

        <!-- Force Enroll Student Page -->
        <div class="container-fluid mt-4">

            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="text-primary mb-4"><i class="bi bi-person-plus"></i> Force Enroll Student</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Force Enroll</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-3">Directly enroll a student in a course without waiting for their request.</p>

                            <form action="<?= base_url('index.php/enrollment/force') ?>" method="post" id="forceEnrollForm">
                                <?= csrf_field() ?>

                                <div class="row mb-3">
                                    <!-- Student Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label for="user_id" class="form-label fw-bold">
                                            Select Student <span class="text-danger">*</span>
                                        </label>
                                        <select id="user_id" name="user_id" required class="form-select">
                                            <option value="">Choose a student...</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?= $user['id'] ?>">
                                                    <?= esc($user['name']) ?> (<?= esc($user['email']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Course Selection -->
                                    <div class="col-md-6 mb-3">
                                        <label for="course_id" class="form-label fw-bold">
                                            Select Course <span class="text-danger">*</span>
                                        </label>
                                        <select id="course_id" name="course_id" required class="form-select">
                                            <option value="">Choose a course...</option>
                                            <?php foreach ($courses as $course): ?>
                                                <option value="<?= $course['id'] ?>">
                                                    <?= esc($course['title']) ?> (<?= esc($course['course_code'] ?? '') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Course Details (shown when course selected) -->
                                <div id="courseDetails" class="mt-4 d-none">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Course Information</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Schedule:</strong> <span id="scheduleInfo">-</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>School Year:</strong> <span id="yearInfo">-</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Semester:</strong> <span id="semesterInfo">-</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Teacher:</strong> <span id="teacherInfo">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Conflict Check Result -->
                                <div id="conflictCheck" class="mt-3 d-none">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <span id="conflictMessage"></span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-4">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">
                                        <i class="bi bi-person-plus"></i> Force Enroll Student
                                    </button>
                                    <a href="<?= base_url('index.php/enrollment/teacher') ?>" class="btn btn-outline-secondary ms-2">
                                        <i class="bi bi-arrow-left"></i> Back to Management
                                    </a>
                                    <p class="text-muted mt-2 small">
                                        This will immediately enroll the student in the selected course, bypassing the approval process.
                                    </p>
                                </div>
        </form>
    </div>
</div>

<script>
const courses = <?= json_encode($courses) ?>;

document.getElementById('course_id').addEventListener('change', function() {
    const courseId = this.value;
    const courseDetails = document.getElementById('courseDetails');
    const conflictCheck = document.getElementById('conflictCheck');

    if (courseId) {
        // Show course details
        courseDetails.classList.remove('hidden');

        // Find course data
        const course = courses.find(c => c.id == courseId);
        if (course) {
            // This would need AJAX call to get full course details including schedule and teacher
            // For now, we'll show basic info and handle conflicts on submit
            document.getElementById('yearInfo').textContent = course.school_year || 'Not set';
            document.getElementById('semesterInfo').textContent = course.semester || 'Not set';
            document.getElementById('scheduleInfo').textContent = course.schedule_days ? course.schedule_days + ' ' + (course.schedule_time_start || '') + '-' + (course.schedule_time_end || '') : 'Not set';
            // We'd need teacher name via AJAX
        }
    } else {
        courseDetails.classList.add('hidden');
        conflictCheck.classList.add('hidden');
    }
});

// Form validation and conflict checking
document.getElementById('forceEnrollForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    submitBtn.disabled = true;

    // Submit form to the correct URL (using base_url to ensure proper routing)
    fetch('<?= base_url('enrollment/force') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student has been successfully force-enrolled!');

            // Reset form and redirect
            this.reset();
            document.getElementById('courseDetails').classList.add('hidden');
            document.getElementById('conflictCheck').classList.add('hidden');
            window.location.href = '<?= base_url('enrollment/teacher') ?>';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the enrollment.');
    })
    .finally(() => {
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
<?= $this->endSection() ?>
