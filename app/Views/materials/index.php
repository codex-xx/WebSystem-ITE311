<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

        <!-- My Materials Page -->
        <div class="container-fluid mt-4">

            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="text-primary mb-4"><i class="bi bi-file-earmark-pdf-fill"></i> My Materials</h2>
            </div>

            <!-- Materials Content -->
            <?php if (empty($groupedMaterials)): ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <p class="text-muted mb-0">No materials available yet. Enroll in courses to access materials.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($groupedMaterials as $courseId => $group): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?= esc($group['course_name']) ?></h5>
                            <span class="badge <?= $group['has_submitted'] ? 'bg-success' : 'bg-warning' ?>">
                                <?= $group['has_submitted'] ? 'Submitted' : 'Not Submitted' ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($group['materials'])): ?>
                                <p class="text-muted mb-3">No materials available for this course yet.</p>
                            <?php else: ?>
                                <div class="list-group mb-3">
                                    <?php foreach ($group['materials'] as $material): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="bi bi-file-earmark-text-fill text-primary"></i>
                                                    <?= esc($material['file_name']) ?>
                                                </h6>
                                                <small class="text-muted">
                                                    Uploaded on: <?= date('M j, Y', strtotime($material['created_at'])) ?>
                                                </small>
                                            </div>
                                            <a href="<?= base_url('/materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Submit Assignment Form -->
                            <h6>Submit Assignment:</h6>
                            <form action="<?= base_url('/student/materials/submit/' . $courseId) ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="assignment_file" accept=".pdf,.ppt,.pptx,.doc,.docx" required>
                                    <div class="form-text">Accepted formats: PDF, PPT, PPTX, DOC, DOCX (Max 10MB)</div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Assignment</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

    </div>

<?= $this->endSection() ?>
