<?= $this->extend('template') ?>

<?= $this->section('title') ?>About<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="content-card">
    <h2 class="text-center mb-4">
        <i class="bi bi-info-circle text-primary me-2"></i>About
    </h2>
    <p class="text-center">
        ITE311-ALBARINA is a Learning Management System for technology education.
    </p>
    <p class="text-center text-muted">
        Built with CodeIgniter 4 and modern web technologies for efficient online learning.
    </p>
</div>

<?= $this->endSection() ?>
