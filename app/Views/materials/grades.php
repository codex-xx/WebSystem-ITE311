<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary mb-0"><i class="bi bi-journal-check"></i> My Grades</h2>
        <a href="<?= base_url('/student/materials') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Materials
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($assignments)): ?>
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted mb-0">No submissions yet. Submit an assignment to see your grades.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Course</th>
                                <th>File</th>
                                <th>Submitted</th>
                                <th>Grade</th>
                                <th>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= esc($assignment['course_name']) ?></td>
                                    <td><?= esc($assignment['file_name']) ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($assignment['submitted_at'])) ?></td>
                                    <td>
                                        <?php if (isset($assignment['grade']) && $assignment['grade'] !== null && $assignment['grade'] !== ''): ?>
                                            <span class="badge bg-success"><?= esc($assignment['grade']) ?></span>
                                            <?php if (!empty($assignment['graded_at'])): ?>
                                                <div class="small text-muted">Graded <?= date('M j, Y g:i A', strtotime($assignment['graded_at'])) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($assignment['feedback'] ?? '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

