<?php
$pages = max(1, (int) ceil($users['total'] / $users['per_page']));
$formUser = $editingUser ?? null;
?>
<div class="card card-soft shadow-sm mb-4">
    <h2 class="section-title h3 mb-3">User management</h2>
    <form method="GET" class="row g-3">
        <div class="col-md-6"><input type="text" class="form-control" name="search" placeholder="Search users" value="<?= htmlspecialchars($filters['search'] ?? '') ?>"></div>
        <div class="col-md-3">
            <select class="form-select" name="role">
                <option value="">All roles</option>
                <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Search</button></div>
    </form>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-soft shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($users['data'] as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge badge-soft"><?= htmlspecialchars($user['role']) ?></span></td>
                            <td><?= $user['deleted_at'] ? 'Inactive' : 'Active' ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('admin/users?edit=' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="<?= base_url('admin/users/' . $user['id'] . '/delete') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <ul class="pagination mb-0">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === (int) $users['page'] ? 'active' : '' ?>"><a class="page-link" href="<?= base_url('admin/users?page=' . $i) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-soft shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0"><?= $formUser ? 'Edit user' : 'Create user' ?></h3>
                <?php if ($formUser): ?><a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">Cancel edit</a><?php endif; ?>
            </div>
            <form method="POST" action="<?= base_url('admin/users/save') ?>" data-validate="true">
                <?= csrf_field() ?>
                <?php if ($formUser): ?><input type="hidden" name="id" value="<?= (int) $formUser['id'] ?>"><?php endif; ?>
                <input type="text" class="form-control mb-2" name="full_name" placeholder="Full name" value="<?= htmlspecialchars(old('full_name', $formUser['full_name'] ?? '')) ?>" data-required="true">
                <input type="email" class="form-control mb-2" name="email" placeholder="Email" value="<?= htmlspecialchars(old('email', $formUser['email'] ?? '')) ?>" data-required="true">
                <input type="text" class="form-control mb-2" name="phone" placeholder="Phone" value="<?= htmlspecialchars(old('phone', $formUser['phone'] ?? '')) ?>">
                <select class="form-select mb-2" name="role" data-required="true">
                    <option value="user" <?= old('role', $formUser['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= old('role', $formUser['role'] ?? 'user') === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <select class="form-select mb-2" name="account_status">
                    <option value="active" <?= old('account_status', ($formUser && $formUser['deleted_at']) ? 'inactive' : 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('account_status', ($formUser && $formUser['deleted_at']) ? 'inactive' : 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <input type="password" class="form-control mb-3" name="password" placeholder="<?= $formUser ? 'New password (optional)' : 'Temporary password' ?>" <?= $formUser ? '' : 'data-required="true"' ?>>
                <button class="btn btn-primary"><?= $formUser ? 'Update user' : 'Save user' ?></button>
            </form>
        </div>
    </div>
</div>
