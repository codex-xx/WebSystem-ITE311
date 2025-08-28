<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-2">
    <div class="col-md-7 col-lg-6">
        <h1 class="text-center mb-4 text-dark">Create Account</h1>

        <?php if (session()->getFlashdata('register_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('register_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 bg-dark text-light">
            <div class="card-body p-4">
                <form action="<?= base_url('register') ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control bg-secondary text-light border-0" id="name" name="name" required value="<?= esc(old('name')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control bg-secondary text-light border-0" id="email" name="email" required value="<?= esc(old('email')) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control bg-secondary text-light border-0" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control bg-secondary text-light border-0" id="password_confirm" name="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Create Account</button>
                </form>
            </div>
        </div>
        <p class="text-center mt-3 text-light small">
            <a href="<?= base_url('login') ?>" class="text-dark">Already have an account? Login</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
