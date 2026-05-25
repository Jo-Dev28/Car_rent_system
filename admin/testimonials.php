<?php
// admin/testimonials.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Approve testimonial
if(isset($_GET['approve'])) {
    $stmt = $pdo->prepare("UPDATE testimonials SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$_GET['approve']]);
    $_SESSION['success'] = "Testimonial approved";
    redirect('testimonials.php');
}

// Delete testimonial
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success'] = "Testimonial deleted";
    redirect('testimonials.php');
}

$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();

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
        <h5><i class="fas fa-star me-2"></i> Customer Testimonials</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($testimonials as $t): ?>
                <tr>
                    <td><?php echo $t['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($t['user_name']); ?></strong><br>
                        <small class="text-muted"><?php echo $t['user_email']; ?></small>
                    </td>
                    <td>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <i class="fas fa-star <?php echo $i<=$t['rating'] ? 'text-warning' : 'text-muted'; ?>" style="font-size: 12px;"></i>
                        <?php endfor; ?>
                    </td>
                    <td style="max-width: 300px;">"<?php echo substr(htmlspecialchars($t['comment']), 0, 100); ?>..."</td>
                    <td>
                        <?php if($t['is_approved']): ?>
                            <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Approved</span>
                        <?php else: ?>
                            <span class="badge bg-warning rounded-pill"><i class="fas fa-clock"></i> Pending</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                    <td>
                        <?php if(!$t['is_approved']): ?>
                            <a href="?approve=<?php echo $t['id']; ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Approve
                            </a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this testimonial?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($testimonials) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3 d-block"></i>
                        No testimonials found
                    </td>
                </tr>
                <?php endif; ?>
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