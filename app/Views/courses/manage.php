<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4">Course Management</h2>

    <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No courses assigned to you. Courses with assigned teachers appear here.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?= esc($course['title']) ?>
                                <small class="text-muted">(<?= esc($course['course_code']) ?>)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong> <?= esc($course['description'] ?? 'No description') ?></p>
                                    <p><strong>School Year:</strong> <?= esc($course['school_year'] ?? 'Not set') ?></p>
                                    <p><strong>Semester:</strong> <?= esc($course['semester'] ?? 'Not set') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Schedule Details:</h6>
                                    <p><strong>Days:</strong> <?= esc($course['schedule_days'] ?? 'Not set') ?></p>
                                    <p><strong>Time Start:</strong> <?= !empty($course['schedule_time_start']) ? date('H:i', strtotime($course['schedule_time_start'])) : 'Not set' ?></p>
                                    <p><strong>Time End:</strong> <?= !empty($course['schedule_time_end']) ? date('H:i', strtotime($course['schedule_time_end'])) : 'Not set' ?></p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#editSchedule<?= $course['id'] ?>">
                                    <i class="bi bi-pencil"></i> Edit Schedule
                                </button>
                                <a href="<?= base_url('course/viewMaterials/' . $course['id']) ?>" class="btn btn-success btn-sm ms-2">
                                    <i class="bi bi-folder"></i> View Materials
                                </a>
                            </div>

                            <div id="editSchedule<?= $course['id'] ?>" class="collapse mt-3">
                                <form action="<?= base_url('course/updateSchedule') ?>" method="post" class="schedule-form">
                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="school_year" class="form-label">School Year</label>
                                            <input type="text" class="form-control" name="school_year" value="<?= esc($course['school_year'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="semester" class="form-label">Semester</label>
                                            <select class="form-control" name="semester" required>
                                                <option value="">Select Semester</option>
                                                <option value="1st" <?= ($course['semester'] == '1st') ? 'selected' : '' ?>>1st Semester</option>
                                                <option value="2nd" <?= ($course['semester'] == '2nd') ? 'selected' : '' ?>>2nd Semester</option>
                                                <option value="Summer" <?= ($course['semester'] == 'Summer') ? 'selected' : '' ?>>Summer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="schedule_days" class="form-label">Schedule Days</label>
                                            <input type="text" class="form-control" name="schedule_days" value="<?= esc($course['schedule_days'] ?? '') ?>" placeholder="e.g., Monday, Wednesday, Friday">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="schedule_time_start" class="form-label">Time Start</label>
                                            <input type="time" class="form-control" name="schedule_time_start" value="<?= esc($course['schedule_time_start']) ?>">
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label for="schedule_time_end" class="form-label">Time End</label>
                                            <input type="time" class="form-control" name="schedule_time_end" value="<?= esc($course['schedule_time_end']) ?>">
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success btn-sm">Update Schedule</button>
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#editSchedule<?= $course['id'] ?>">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.schedule-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Schedule updated successfully!');
                    location.reload(); // Reload to show updated data
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the schedule: ' + error.message);
            });
        });
    });
});
</script>

<?= $this->endSection() ?>
