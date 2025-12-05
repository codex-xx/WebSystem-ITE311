<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <!-- Back to Dashboard -->
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Students Header -->
    <div class="mb-4">
        <h2 class="text-primary mb-4"><i class="bi bi-people-fill"></i> My Students</h2>
    </div>

    <!-- Students List -->
    <?php if (!empty($students)): ?>
        <div class="row">
            <?php foreach ($students as $student): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <i class="bi bi-person-circle text-primary"></i> <?= esc($student['name']) ?>
                            </h5>
                            <p class="card-text text-muted mb-3">
                                <strong>Email:</strong> <?= esc($student['email']) ?><br>
                                <strong>Status:</strong>
                                <span class="badge bg-<?= $student['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(esc($student['status'])) ?>
                                </span>
                            </p>
                            <div class="mt-auto">
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-info-circle"></i> View assignments in course materials management.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted mb-0">No students found.</p>
            </div>
        </div>
    <?php endif; ?>

</div>

</div>

<?= $this->endSection() ?>
