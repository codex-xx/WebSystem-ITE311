<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="mb-4" style="color: #2c3e50; font-weight: 600;">
            <?= $role === 'admin' ? 'Course Management Dashboard' : 'My Courses' ?>
        </h2>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Interface: Full Course Management -->
            <!-- Summary Cards - Only for Admin -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                        <div class="card-body text-center">
                            <h3 class="card-title" style="color: #3498db; font-size: 2.5rem; font-weight: bold;"><?= $totalCourses ?></h3>
                            <p class="card-text" style="color: #7f8c8d; font-size: 1.1rem;">Total Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                        <div class="card-body text-center">
                            <h3 class="card-title" style="color: #27ae60; font-size: 2.5rem; font-weight: bold;"><?= $activeCourses ?></h3>
                            <p class="card-text" style="color: #7f8c8d; font-size: 1.1rem;">Active Courses</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="card shadow-sm mb-4" style="border: none; border-radius: 10px;">
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by course title, code, or teacher..." style="border-radius: 25px 0 0 25px; border: 1px solid #ddd; padding: 10px 20px;">
                        <button class="btn btn-primary" type="button" style="border-radius: 0 25px 25px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 10px 20px;">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="card shadow-sm" style="border: none; border-radius: 10px;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="coursesTable">
                            <thead style="background-color: #f1f3f4;">
                                <tr>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Course Code</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Course Title</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Description</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">School Year</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Semester</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Schedule</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Teacher</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Status</th>
                                    <th style="border: none; font-weight: 600; color: #2c3e50;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr style="border-bottom: 1px solid #f1f3f4;">
                                        <td style="vertical-align: middle; color: #34495e;"><?= esc($course['course_code']) ?></td>
                                        <td style="vertical-align: middle; color: #34495e; font-weight: 500;"><?= esc($course['title']) ?></td>
                                        <td style="vertical-align: middle; color: #7f8c8d; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($course['description']) ?>">
                                            <?= esc($course['description'] ?? 'No description') ?>
                                        </td>
                                        <td style="vertical-align: middle; color: #34495e;"><?= esc($course['school_year']) ?></td>
                                        <td style="vertical-align: middle; color: #34495e;"><?= esc($course['semester']) ?></td>
                                        <td style="vertical-align: middle; color: #34495e;">
                                            <?php
                                            $schedule = [];
                                            if (!empty($course['schedule_days'])) $schedule[] = esc($course['schedule_days']);
                                            if (!empty($course['schedule_time_start']) && !empty($course['schedule_time_end'])) {
                                                $schedule[] = date('H:i', strtotime($course['schedule_time_start'])) . ' - ' . date('H:i', strtotime($course['schedule_time_end']));
                                            }
                                            echo implode('<br>', $schedule) ?: 'Not set';
                                            ?>
                                        </td>
                                        <td style="vertical-align: middle; color: #34495e;"><?= esc($course['teacher_name'] ?? 'Not assigned') ?></td>
                                        <td style="vertical-align: middle;">
                                            <span class="badge <?= $course['status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>" style="font-size: 0.85em;">
                                                <?= esc($course['status']) ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <button class="btn btn-sm btn-outline-primary edit-course-btn"
                                                    data-course-id="<?= $course['id'] ?>"
                                                    data-course-code="<?= esc($course['course_code']) ?>"
                                                    data-title="<?= esc($course['title']) ?>"
                                                    data-description="<?= esc($course['description']) ?>"
                                                    data-school-year="<?= esc($course['school_year']) ?>"
                                                    data-semester="<?= esc($course['semester']) ?>"
                                                    data-start-date="<?= esc($course['start_date']) ?>"
                                                    data-end-date="<?= esc($course['end_date']) ?>"
                                                    data-teacher-id="<?= esc($course['teacher_id']) ?>"
                                                    data-schedule="<?= esc($course['schedule_days']) ?>"
                                                    data-schedule-time-start="<?= esc($course['schedule_time_start']) ?>"
                                                    data-schedule-time-end="<?= esc($course['schedule_time_end']) ?>"
                                                    data-status="<?= esc($course['status']) ?>"
                                                    style="border-radius: 20px;">
                                                Edit Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary" style="border-radius: 25px; padding: 8px 20px;">Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Upload Materials Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 15px 15px 0 0;">
                <h6 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Upload Materials
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" id="upload_course_id" name="course_id">

                    <div class="mb-3">
                        <label for="upload_course_title" class="form-label fw-bold">Course</label>
                        <input type="text" class="form-control" id="upload_course_title" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="mb-3">
                        <label for="material_title" class="form-label fw-bold">Material Title</label>
                        <input type="text" class="form-control" id="material_title" name="material_title" placeholder="Enter material title (optional)">
                    </div>

                    <div class="mb-3">
                        <label for="material_file" class="form-label fw-bold">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="material_file" name="material_file" accept=".pdf,.ppt,.pptx,.doc,.docx" required>
                        <div class="form-text">
                            Allowed formats: PDF, PPT, PPTX, DOC, DOCX. Maximum size: 10MB.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadBtn">
                    <i class="bi bi-cloud-upload me-1"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm">
                    <input type="hidden" id="course_id" name="course_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="course_code" class="form-label" style="font-weight: 500;">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label for="title" class="form-label" style="font-weight: 500;">Course Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="school_year" class="form-label" style="font-weight: 500;">School Year</label>
                            <input type="text" class="form-control" id="school_year" name="school_year" required>
                        </div>
                        <div class="col-md-6">
                            <label for="semester" class="form-label" style="font-weight: 500;">Semester</label>
                            <select class="form-control" id="semester" name="semester" required>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label" style="font-weight: 500;">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label" style="font-weight: 500;">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label" style="font-weight: 500;">Teacher</label>
                            <select class="form-control" id="teacher_id" name="teacher_id">
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="schedule" class="form-label" style="font-weight: 500;">Schedule Days</label>
                            <input type="text" class="form-control" id="schedule" name="schedule" placeholder="e.g., Monday, Wednesday, Friday">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="schedule_time_start" class="form-label" style="font-weight: 500;">Start Time</label>
                            <input type="time" class="form-control" id="schedule_time_start" name="schedule_time_start">
                        </div>
                        <div class="col-md-6">
                            <label for="schedule_time_end" class="form-label" style="font-weight: 500;">End Time</label>
                            <input type="time" class="form-control" id="schedule_time_end" name="schedule_time_end">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label" style="font-weight: 500;">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label" style="font-weight: 500;">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 25px; padding: 8px 20px;">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateCourseBtn" style="border-radius: 25px; padding: 8px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload modal
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

    // Search functionality (only for admin)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        const coursesTable = document.getElementById('coursesTable');
        const tbody = coursesTable.querySelector('tbody');
        const originalRows = Array.from(tbody.querySelectorAll('tr'));

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            originalRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Open upload modal function
    window.openUploadModal = function(courseId, courseTitle) {
        document.getElementById('upload_course_id').value = courseId;
        document.getElementById('upload_course_title').value = courseTitle;
        document.getElementById('material_title').value = '';
        document.getElementById('material_file').value = '';
        uploadModal.show();
    };



    // Handle upload
    document.getElementById('uploadBtn').addEventListener('click', function() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);

        const fileInput = document.getElementById('material_file');
        if (!fileInput.files[0]) {
            alert('Please select a file to upload.');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass me-1"></i>Uploading...';

        fetch('/course/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                // Success - redirect to the upload page
                window.location.href = response.url;
            } else {
                return response.text().then(text => {
                    throw new Error(text);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Upload failed. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Upload';
        });
    });

    // Edit course modal
    const editCourseModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
    const editButtons = document.querySelectorAll('.edit-course-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Populate modal with course data
            document.getElementById('course_id').value = this.dataset.courseId;
            document.getElementById('course_code').value = this.dataset.courseCode;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('description').value = this.dataset.description;
            document.getElementById('school_year').value = this.dataset.schoolYear;
            document.getElementById('semester').value = this.dataset.semester;
            document.getElementById('start_date').value = this.dataset.startDate;
            document.getElementById('end_date').value = this.dataset.endDate;
            document.getElementById('teacher_id').value = this.dataset.teacherId;
            document.getElementById('schedule').value = this.dataset.schedule;
            document.getElementById('schedule_time_start').value = this.dataset.scheduleTimeStart;
            document.getElementById('schedule_time_end').value = this.dataset.scheduleTimeEnd;
            document.getElementById('status').value = this.dataset.status;

            editCourseModal.show();
        });
    });

    // Update course
    document.getElementById('updateCourseBtn').addEventListener('click', function() {
        const form = document.getElementById('editCourseForm');
        const formData = new FormData(form);

        // Basic client-side validation for dates
        const startDate = formData.get('start_date');
        const endDate = formData.get('end_date');

        if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
            alert('End date must be after start date.');
            return;
        }

        fetch('<?= base_url('course/updateCourse') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Course details updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the course.');
        });
    });
});
</script>

<?= $this->endSection() ?>
