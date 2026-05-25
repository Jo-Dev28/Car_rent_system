<?php
// admin/contacts.php
require_once '../includes/config.php';

if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    redirect('../login.php');
}

// Mark as read
if(isset($_GET['read'])) {
    $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?");
    $stmt->execute([$_GET['read']]);
    redirect('contacts.php');
}

// Delete message
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $_SESSION['success'] = "Message deleted";
    redirect('contacts.php');
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();

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
        <h5><i class="fas fa-envelope me-2"></i> Contact Messages</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contacts as $contact): ?>
                <tr>
                    <td><?php echo $contact['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($contact['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                    <td><?php echo htmlspecialchars($contact['subject'] ?: 'No subject'); ?></td>
                    <td style="max-width: 250px;"><?php echo substr(htmlspecialchars($contact['message']), 0, 80); ?>...</td>
                    <td>
                        <?php if($contact['is_read']): ?>
                            <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i> Read</span>
                        <?php else: ?>
                            <span class="badge bg-warning rounded-pill"><i class="fas fa-clock"></i> Unread</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y H:i', strtotime($contact['created_at'])); ?></td>
                    <td>
                        <a href="?read=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-envelope-open"></i>
                        </a>
                        <a href="?delete=<?php echo $contact['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($contacts) == 0): ?>
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        No messages found
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