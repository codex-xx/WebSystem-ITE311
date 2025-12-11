<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <!-- Course Management Interface -->
    <div class="mb-4">
        <h2 class="text-primary mb-4"><i class="bi bi-book"></i> Course Material Management</h2>
        <p class="text-muted mb-3">Select a course to upload materials.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($courses as $course): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary"><?= esc($course['title']) ?></h5>
                        <p class="card-text text-muted mb-3">
                            <?= esc($course['description'] ?? 'No description available.') ?>
                        </p>

                        <?php
                        if (!isset($materialModel)) {
                            $materialModel = new \App\Models\MaterialModel();
                        }
                        $materialsCount = $materialModel->getMaterialsCountByCourse($course['id']);
                        $materials = $materialModel->getMaterialsByCourse($course['id']);
                        ?>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-file-earmark"></i>
                                Materials: <?= $materialsCount ?>
                            </small>

                            <?php if ($materialsCount > 0): ?>
                            <div class="mt-2">
                                <small class="text-muted d-block">Recent files:</small>
                                <ul class="list-unstyled small">
                                    <?php foreach (array_slice($materials, 0, 2) as $material): ?>
                                        <li>
                                            <i class="bi bi-file-earmark"></i>
                                            <?= esc($material['title'] ?? $material['file_name']) ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if ($materialsCount > 2): ?>
                                        <li class="text-muted">+<?= $materialsCount - 2 ?> more...</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-auto">
                            <button class="btn btn-primary w-100"
                                    onclick="openUploadModal(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')">
                                <i class="bi bi-upload"></i> Upload Material
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($assignments)): ?>
        <!-- Student Assignments Section -->
        <div class="mt-5">
            <h3 class="text-primary mb-4">
                <i class="bi bi-file-earmark-check-fill"></i>
                Student Assignment Submissions
            </h3>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="bi bi-person"></i> Student Name</th>
                                    <th><i class="bi bi-envelope"></i> Email</th>
                                    <th><i class="bi bi-book"></i> Course</th>
                                    <th><i class="bi bi-file-earmark-text"></i> Assignment File</th>
                                    <th><i class="bi bi-calendar"></i> Submitted On</th>
                                    <th><i class="bi bi-check2-circle"></i> Grade</th>
                                    <th><i class="bi bi-chat"></i> Feedback</th>
                                    <th><i class="bi bi-download"></i> Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($assignment['student_name']) ?></strong>
                                        </td>
                                        <td>
                                            <?= esc($assignment['student_email']) ?>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= esc($assignment['course_title']) ?></small>
                                        </td>
                                        <td>
                                            <i class="bi bi-file-earmark-text-fill text-primary"></i>
                                            <?= esc($assignment['file_name']) ?>
                                        </td>
                                        <td>
                                            <?= date('M j, Y, g:i A', strtotime($assignment['submitted_at'])) ?>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <input type="text"
                                                   class="form-control form-control-sm grade-input"
                                                   value="<?= esc($assignment['grade'] ?? '') ?>"
                                                   placeholder="e.g. 95 or A"
                                                   data-assignment-id="<?= $assignment['id'] ?>">
                                        </td>
                                        <td style="min-width: 180px;">
                                            <input type="text"
                                                   class="form-control form-control-sm feedback-input"
                                                   value="<?= esc($assignment['feedback'] ?? '') ?>"
                                                   placeholder="Optional feedback">
                                            <?php if (!empty($assignment['graded_at'])): ?>
                                                <div class="small text-muted mt-1">Graded: <?= date('M j, Y g:i A', strtotime($assignment['graded_at'])) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="d-flex gap-1">
                                                <a href="<?= base_url('/assignments/download/' . $assignment['id']) ?>"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Download Assignment">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button class="btn btn-sm btn-success grade-btn"
                                                        data-assignment-id="<?= $assignment['id'] ?>">
                                                    <i class="bi bi-send"></i> Save
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($courses)): ?>
        <div class="text-center py-5">
            <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
            <h4 class="text-muted mt-3">No Courses Available</h4>
            <p class="text-muted">No courses to manage at the moment.</p>
        </div>
    <?php endif; ?>

</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-upload"></i> Upload Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="modalCourseId" name="course_id">

                    <div class="mb-3">
                        <label for="material_file" class="form-label">
                            <i class="bi bi-file-earmark"></i> Select File
                        </label>
                        <input type="file" class="form-control" id="material_file" name="material_file" required accept=".pdf,.ppt,.pptx,.doc,.docx">
                        <div class="form-text">
                            <strong>Allowed types:</strong> PDF, PPT, PPTX, DOC, DOCX
                            <br><strong>Maximum size:</strong> 10MB
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="bi bi-info-circle"></i> Upload Instructions
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Add a clear title for the material</li>
                                    <li>Select a file from your computer</li>
                                    <li>Ensure it's one of the allowed file types</li>
                                    <li>File size should not exceed 10MB</li>
                                    <li>Click "Upload" to save the material</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="material_title" class="form-label">
                            <i class="bi bi-type"></i> Material Title
                        </label>
                        <input type="text" class="form-control" id="material_title" name="material_title" required maxlength="255" placeholder="Enter a title for this material">
                    </div>

                    <div id="uploadProgress" class="d-none">
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">Uploading... Please wait.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitUpload">
                        <i class="bi bi-upload"></i> Upload Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUploadModal(courseId, courseTitle) {
    document.getElementById('uploadModalLabel').innerHTML = '<i class="bi bi-upload"></i> Upload Material - ' + courseTitle;
    document.getElementById('modalCourseId').value = courseId;

    // Reset form
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadProgress').classList.add('d-none');

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}



document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitUpload');
    const progressBar = document.querySelector('.progress-bar');
    const progressContainer = document.getElementById('uploadProgress');
    const courseId = document.getElementById('modalCourseId').value;

    // Disable button and show progress
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Uploading...';

    // Show progress
    progressContainer.classList.remove('d-none');
    progressBar.style.width = '0%';

    fetch('<?= base_url('course/') ?>' + courseId + '/upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Check if response is successful (200-399 range)
        if (response.status >= 200 && response.status < 400) {
            // Success - close modal and show success message
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            showAlert('<i class="bi bi-check-circle"></i> Upload completed successfully!', 'success');

            // Reload page to show updated materials count
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            // Try to get error message from response
            return response.text().then(text => {
                throw new Error(text || 'Upload failed with status: ' + response.status);
            });
        }
    })
    .catch(error => {
        // Handle any errors
        showAlert('<i class="bi bi-exclamation-triangle"></i> Upload failed. Please try again.', 'danger');
        console.error('Upload error:', error);
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-upload"></i> Upload Material';
        progressContainer.classList.add('d-none');
    });

    // Simulate progress (for demo purposes)
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) {
            clearInterval(progressInterval);
            return;
        }
        progressBar.style.width = progress + '%';
    }, 200);
});

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" role="alert"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) alert.remove();
    }, 5000);
}

// File validation on client side
document.getElementById('material_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const allowedTypes = ['application/pdf', 'application/vnd.ms-powerpoint',
                         'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                         'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    const maxSize = 10 * 1024 * 1024; // 10MB

    if (file) {
        if (!allowedTypes.includes(file.type)) {
            showAlert('<i class="bi bi-exclamation-triangle"></i> Invalid file type. Please select a PDF, PPT, PPTX, DOC, or DOCX file.', 'danger');
            e.target.value = '';
        } else if (file.size > maxSize) {
            showAlert('<i class="bi bi-exclamation-triangle"></i> File size too large. Please select a file smaller than 10MB.', 'danger');
            e.target.value = '';
        }
    }
});

// Grade submission
document.querySelectorAll('.grade-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const assignmentId = this.getAttribute('data-assignment-id');
        const gradeInput = row.querySelector('.grade-input');
        const feedbackInput = row.querySelector('.feedback-input');

        const payload = new FormData();
        payload.append('grade', gradeInput.value.trim());
        payload.append('feedback', feedbackInput.value.trim());

        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split"></i>';

        fetch('<?= base_url('/assignments/grade/') ?>' + assignmentId, {
            method: 'POST',
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('<i class="bi bi-check-circle"></i> Grade saved', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                throw new Error(data.message || 'Failed to save grade');
            }
        })
        .catch(err => {
            showAlert('<i class="bi bi-exclamation-triangle"></i> ' + err.message, 'danger');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-send"></i> Save';
        });
    });
});
</script>

<?= $this->endSection() ?>
