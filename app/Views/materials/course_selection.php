<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <!-- Course Selection Interface -->
    <div class="mb-4">
        <h2 class="text-primary mb-4"><i class="bi bi-book"></i> Course Material Management</h2>
        <p class="text-muted mb-3">Select a course to upload materials or manage existing files.</p>
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

                        // Get assignment count for this course
                        $assignmentModel = new \App\Models\AssignmentModel();
                        $assignmentCount = $assignmentModel->where('course_id', $course['id'])->countAllResults();
                        ?>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-file-earmark"></i>
                                Materials: <?= $materialsCount ?> |
                                <i class="bi bi-journal-check"></i>
                                Assignments: <?= $assignmentCount ?>
                            </small>
                        </div>

                        <div class="mt-auto">
                            <a href="/course/manage/<?= $course['id'] ?>" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-folder"></i> Manage Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($courses)): ?>
        <div class="text-center py-5">
            <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
            <h4 class="text-muted mt-3">No Courses Assigned</h4>
            <p class="text-muted">No courses assigned to you at the moment.</p>
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
