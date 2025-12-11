<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">
                <i class="bi bi-folder"></i> Course Materials
            </h2>
            <p class="text-muted mb-0">
                <strong>Course:</strong> <?= isset($course->title) ? esc($course->title) : 'Unknown' ?> (<?= isset($course->course_code) ? esc($course->course_code) : 'N/A' ?>)
            </p>
        </div>
        <div>
            <a href="/materials/manage" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Courses
            </a>
            <?php if (isset($course->id)): ?>
            <button class="btn btn-primary ms-2" onclick="openUploadModal(<?= $course->id ?>, '<?= isset($course->title) ? esc($course->title) : 'Unknown' ?>')">
                <i class="bi bi-cloud-upload me-1"></i>Upload Material
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($materials)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                <h4 class="text-muted mt-3">No Materials Uploaded</h4>
                <p class="text-muted">No materials have been uploaded for this course yet.</p>
                <?php if (isset($course->id)): ?>
                <button class="btn btn-primary" onclick="openUploadModal(<?= $course->id ?>, '<?= isset($course->title) ? esc($course->title) : 'Unknown' ?>')">
                    <i class="bi bi-cloud-upload me-1"></i>Upload First Material
                </button>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-files me-2"></i>
                    Uploaded Materials (<?= count($materials) ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($materials as $material): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body">
                                    <h6 class="card-title text-truncate" title="<?= esc($material['title'] ?? $material['file_name']) ?>">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <?= esc($material['title'] ?? $material['file_name']) ?>
                                    </h6>
                                    <p class="card-text small text-muted">
                                        <strong>File:</strong> <?= esc($material['file_name']) ?><br>
                                        <strong>Uploaded:</strong> <?= date('M j, Y H:i', strtotime($material['uploaded_at'])) ?>
                                    </p>
                                    <div class="d-flex gap-1">
                                        <a href="/materials/download/<?= $material['id'] ?>"
                                           class="btn btn-sm btn-outline-primary flex-fill"
                                           title="Download">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="deleteMaterial(<?= $material['id'] ?>, '<?= esc(addslashes($material['title'] ?? $material['file_name'])) ?>')"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this material?</p>
                <p class="text-danger fw-bold" id="deleteMaterialName"></p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Material
                </button>
            </div>
        </div>
    </div>
</div>



<script>
let materialToDelete = null;
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

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

function deleteMaterial(materialId, materialName) {
    materialToDelete = materialId;
    document.getElementById('deleteMaterialName').textContent = materialName;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!materialToDelete) return;

    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass me-1"></i>Deleting...';

    fetch('/materials/delete/' + materialToDelete, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.redirected) {
            // Success - redirect back to the materials page
            window.location.reload();
        } else {
            throw new Error('Delete failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('<i class="bi bi-exclamation-triangle"></i> Failed to delete material. Please try again.', 'danger');
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-trash me-1"></i>Delete Material';
        deleteModal.hide();
    });
});

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
            // Success - close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            showAlert('<i class="bi bi-check-circle"></i> Upload completed successfully!', 'success');
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
</script>

<?= $this->endSection() ?>
