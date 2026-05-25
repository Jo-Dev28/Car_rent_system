<?php
// admin/users.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Delete user
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success'] = "User deleted";
    redirect('users.php');
}

// Toggle user status
if(isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include 'includes/sidebar.php';
?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Page Content -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h5><i class="fas fa-users me-2"></i> All Users</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?> rounded-pill">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?> rounded-pill">
                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if($user['role'] != 'admin'): ?>
                            <a href="?toggle=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                <i class="fas fa-toggle-<?php echo $user['is_active'] ? 'off' : 'on'; ?>"></i>
                            </a>
                            <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted"><i class="fas fa-shield-alt"></i> Admin</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
echo '</div></div>';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>