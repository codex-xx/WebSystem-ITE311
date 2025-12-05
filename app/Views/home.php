<?= $this->extend('template') ?>

<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-card">
    <h2 class="text-center mb-4">
        <i class="bi bi-house text-primary me-2"></i>Welcome to ITE311-ALBARINA
    </h2>
    <p class="text-center lead">
        Learning Management System for Technology Education
    </p>

    <div class="text-center">
        <?php if (!session()->get('isLoggedIn')): ?>
            <a href="<?= base_url('/login') ?>" class="btn btn-primary btn-lg me-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
        <?php else: ?>
            <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        <?php endif; ?>
        <a href="<?= base_url('/courses') ?>" class="btn btn-outline-primary btn-lg ms-2">
            <i class="bi bi-book me-2"></i>Courses
        </a>
    </div>
</div>

<?= $this->endSection() ?>
