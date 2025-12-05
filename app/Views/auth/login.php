<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<div class="content-card">
    <h1 class="text-center mb-4">
        <i class="bi bi-box-arrow-in-right text-primary me-2"></i>Sign In
    </h1>

    <?php if (session()->getFlashdata('register_success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= esc(session()->getFlashdata('register_success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('login_error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= esc(session()->getFlashdata('login_error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="<?= base_url('login') ?>" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?= esc(old('email')) ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Enter your password</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3 text-muted small">
                Don't have an account? <a href="<?= base_url('register') ?>">Register</a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
