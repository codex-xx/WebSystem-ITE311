<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-journal-check"></i> My Enrollments</h5>
                </div>
                <div class="card-body">

                    <?php if (empty($enrollments)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You haven't enrolled in any courses yet.
                            <a href="<?= base_url('/courses') ?>" class="alert-link">Browse our course catalog</a> to get started!
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Course</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Enrolled</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($enrollment['course_name']) ?></strong>
                                            </td>
                                            <td>
                                                <?= esc($enrollment['description']) ?: 'No description available' ?>
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
                                                <?php if ($enrollment['status'] === 'approved' || $enrollment['status'] === 'force_enrolled'): ?>
                                                    <a href="<?= base_url('index.php/student/materials') ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-book"></i> View Materials
                                                    </a>
                                                <?php elseif ($enrollment['status'] === 'pending'): ?>
                                                    <small class="text-muted">Waiting for approval</small>
                                                <?php elseif ($enrollment['status'] === 'denied'): ?>
                                                    <small class="text-danger">Request denied</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 text-center">
                        <a href="<?= base_url('index.php/courses') ?>" class="btn btn-success">
                            <i class="bi bi-search"></i> Browse Courses
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
