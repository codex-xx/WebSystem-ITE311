<?php
// Check if we have users data (for user management) or courses data (for course management)
$users = $users ?? [];
$courses = $courses ?? [];
$hasUsers = !empty($users);
$hasCourses = !empty($courses);
$userRole = $role ?? session()->get('role');
$userName = $user_name ?? session()->get('user_name');
?>

<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
<?= $hasUsers ? 'User Management' : 'Course Management' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <?php if ($hasUsers): ?>
        <!-- User Management Interface -->
        <div class="mb-4">
            <h2 class="text-primary mb-0"><i class="bi bi-people"></i> User Management</h2>
        </div>

        <!-- Add User Button -->
        <div class="mb-4">
            <button type="button" class="btn btn-primary" id="showAddUserForm">
                <i class="bi bi-person-plus"></i> Add New User
            </button>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addUserModalLabel">
                            <i class="bi bi-person-plus"></i> Add New User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="newUserName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="newUserName" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="newUserEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="newUserEmail" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="newUserRole" class="form-label">Role</label>
                                    <select class="form-control" id="newUserRole" required>
                                        <option value="student">Student</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="newUserPassword" class="form-label">Password</label>
                                    <input type="text" class="form-control" id="newUserPassword" value="123456" readonly>
                                    <div class="mt-2 small text-muted">Default password is "123456". Users can change their password after login.</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="addUserBtn" form="addUserForm">
                            <i class="bi bi-person-plus"></i> Add User
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <p class="text-muted">Manage users: edit details, change roles, and deactivate user accounts. <strong class="text-warning">Admin users cannot be deactivated for security.</strong> Deactivated users remain in the list but are marked as inactive.</p>
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

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr data-user-id="<?= $user['id'] ?>" style="<?php if (($user['status'] ?? 'active') === 'inactive'): ?>opacity: 0.6;<?php endif; ?>">
                                    <td class="text-center align-middle">
                                        <strong><?= esc($user['id']) ?></strong>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm user-name"
                                               value="<?= esc($user['name']) ?>" data-original="<?= esc($user['name']) ?>" <?php if (($user['status'] ?? 'active') === 'inactive'): ?>disabled<?php endif; ?>>
                                    </td>
                                    <td>
                                        <input type="email" class="form-control form-control-sm user-email"
                                               value="<?= esc($user['email']) ?>" data-original="<?= esc($user['email']) ?>" <?php if (($user['status'] ?? 'active') === 'inactive'): ?>disabled<?php endif; ?>>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm user-role" data-original="<?= esc($user['role']) ?>" <?= ($user['role'] === 'admin' || ($user['status'] ?? 'active') === 'inactive') ? 'disabled' : '' ?>>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <option value="admin" selected>Admin</option>
                                            <?php else: ?>
                                                <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                                <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge <?php if (($user['status'] ?? 'active') === 'active'): ?>bg-success<?php else: ?>bg-danger<?php endif; ?>">
                                            <?= esc(($user['status'] ?? 'active') === 'active' ? 'Active' : 'Deactivated') ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small align-middle">
                                        <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success btn-sm save-user-btn"
                                                    data-user-id="<?= $user['id'] ?>"
                                                    onclick="saveUser(<?= $user['id'] ?>, this)" <?php if (($user['status'] ?? 'active') === 'inactive'): ?>disabled<?php endif; ?>>
                                                <i class="bi bi-check"></i> Save
                                            </button>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-shield-lock"></i> Protected
                                                </button>
                                            <?php elseif (($user['status'] ?? 'active') === 'active'): ?>
                                                <button class="btn btn-sm btn-outline-danger deactivate-user-btn"
                                                        data-user-id="<?= $user['id'] ?>"
                                                        data-user-name="<?= esc($user['name']) ?>"
                                                        onclick="deactivateUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>')">
                                                    <i class="bi bi-person-dash"></i> Deactivate
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-success activate-user-btn"
                                                        data-user-id="<?= $user['id'] ?>"
                                                        data-user-name="<?= esc($user['name']) ?>"
                                                        onclick="activateUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>')">
                                                    <i class="bi bi-person-check"></i> Activate
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($hasCourses): ?>
        <!-- Course Management Interface -->
        <div class="mb-4">
            <h2 class="text-primary mb-4"><i class="bi bi-book"></i> Course Management</h2>
            <p class="text-muted mb-3">Upload materials and manage course content.</p>
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
                            ?>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-file-earmark"></i>
                                    Materials: <?= $materialsCount ?>
                                </small>
                            </div>

                            <div class="mt-auto">
                                <a href="<?= base_url('/admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-primary w-100">
                                    <i class="bi bi-upload"></i> Upload Material
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
            <h4 class="text-muted mt-3">No Data Available</h4>
            <p class="text-muted">Access denied or no data to display.</p>
        </div>
    <?php endif; ?>
</div>



<script>


// User Management JavaScript Functions
function saveUser(userId, button) {
    var row = button.closest('tr');
    var name = row.querySelector('.user-name').value.trim();
    var email = row.querySelector('.user-email').value.trim();
    var roleSelect = row.querySelector('.user-role');
    var role = roleSelect.disabled ? roleSelect.getAttribute('data-original') : roleSelect.value;

    // Validate required fields
    if (!name || !email || !role) {
        alert('All fields required');
        return;
    }

    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass"></i> Saving...';

    var formData = new FormData();
    formData.append('user_id', userId);
    formData.append('name', name);
    formData.append('email', email);
    formData.append('role', role);

    fetch('<?= base_url('user/update') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the original data
            row.querySelector('.user-name').setAttribute('data-original', name);
            row.querySelector('.user-email').setAttribute('data-original', email);
            row.querySelector('.user-role').setAttribute('data-original', role);
            alert('Saved!');
        } else {
            // Handle "Nothing changed" message differently
            if (data.message && data.message.includes('Nothing changed')) {
                alert('Nothing changed. No update needed.');
            } else {
                alert('Error: ' + data.message);
            }
        }
    })
    .catch(error => {
        alert('Network error');
        console.error('Error:', error);
    })
    .finally(() => {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-check"></i> Save';
    });
}

function deactivateUser(userId, userName) {
    if (confirm('Deactivate ' + userName + '? This action can be reversed by admins.')) {
        var formData = new FormData();
        formData.append('user_id', userId);

        fetch('<?= base_url('user/deactivate') ?>/' + userId, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deactivated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error');
            console.error('Error:', error);
        });
    }
}

function activateUser(userId, userName) {
    if (confirm('Activate ' + userName + '? This will restore their account access.')) {
        var formData = new FormData();
        formData.append('user_id', userId);

        fetch('<?= base_url('user/activate') ?>/' + userId, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User activated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error');
            console.error('Error:', error);
        });
    }
}

// Add User JavaScript Functions
document.addEventListener('DOMContentLoaded', function() {
    // Show Add User Modal button
    document.getElementById('showAddUserForm').addEventListener('click', function() {
        // Reset form when opening modal
        document.getElementById('addUserForm').reset();

        // Set default password (read-only, always "123456")
        document.getElementById('newUserPassword').value = '123456';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
        modal.show();
    });

    // Add user form submission
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('newUserName').value.trim();
        const email = document.getElementById('newUserEmail').value.trim();
        const password = document.getElementById('newUserPassword').value;
        const role = document.getElementById('newUserRole').value;
        const addBtn = document.getElementById('addUserBtn');

        // Validate required fields
        if (!name || !email || !password || !role) {
            alert('All fields are required!');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address!');
            return;
        }

        // Disable button and show loading
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="bi bi-hourglass"></i> Creating User...';

        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('role', role);

        fetch('<?= base_url('user/add') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload page to show new user
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error: ' + error.message);
            console.error('Error:', error);
        })
        .finally(() => {
            // Re-enable button
            addBtn.disabled = false;
            addBtn.innerHTML = '<i class="bi bi-person-plus"></i> Add User';
        });
    });
});
</script>

<?= $this->endSection() ?>
