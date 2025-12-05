<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="col-lg-10 mx-auto">
        <h1 class="text-center mb-4">My Profile</h1>



        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Profile Information Display -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Full Name:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?= esc($user['name']) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?= esc($user['email']) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Role:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-secondary fs-6 p-2"><?= ucfirst(esc($user['role'])) ?></span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <strong>Account Status:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-success fs-6 p-2">Active</span>
                        <small class="text-muted d-block mt-1">Your account is active and in good standing.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-key-fill me-2"></i>Change Password</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('profile') ?>" method="post">
                    <input type="hidden" name="csrf_test_name" value="<?= csrf_hash() ?>" />

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Password Requirements:</strong>
                        <ul class="mb-0 mt-2">
                            <li>At least 6 characters long</li>
                            <li>Enter your current password for verification</li>
                            <li>Confirm your new password</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <?php if ($validation && $validation->hasError('current_password')): ?>
                            <div class="text-danger mt-1"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= $validation->getError('current_password') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                <div class="form-text">Minimum 6 characters</div>
                                <?php if ($validation && $validation->hasError('new_password')): ?>
                                    <div class="text-danger mt-1"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= $validation->getError('new_password') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <?php if ($validation && $validation->hasError('confirm_password')): ?>
                                    <div class="text-danger mt-1"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= $validation->getError('confirm_password') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-key me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>
