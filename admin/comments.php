<?php
/**
 * Admin Comments Management
 */
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle actions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
        
        switch ($_POST['action']) {
            case 'approve':
                if (update_comment_status($comment_id, 'approved')) {
                    $message = 'Comment approved successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to approve comment';
                    $message_type = 'error';
                }
                break;
                
            case 'reject':
                if (update_comment_status($comment_id, 'rejected')) {
                    $message = 'Comment rejected successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to reject comment';
                    $message_type = 'error';
                }
                break;
                
            case 'delete':
                if (delete_comment($comment_id)) {
                    $message = 'Comment deleted successfully';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to delete comment';
                    $message_type = 'error';
                }
                break;
                
            case 'bulk_approve':
                $comment_ids = isset($_POST['comment_ids']) ? $_POST['comment_ids'] : [];
                $count = 0;
                foreach ($comment_ids as $id) {
                    if (update_comment_status((int)$id, 'approved')) {
                        $count++;
                    }
                }
                $message = "$count comment(s) approved successfully";
                $message_type = 'success';
                break;
                
            case 'bulk_delete':
                $comment_ids = isset($_POST['comment_ids']) ? $_POST['comment_ids'] : [];
                $count = 0;
                foreach ($comment_ids as $id) {
                    if (delete_comment((int)$id)) {
                        $count++;
                    }
                }
                $message = "$count comment(s) deleted successfully";
                $message_type = 'success';
                break;
        }
    }
}

// Get filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get all comments
$all_comments = get_all_comments();

// Filter comments
if ($status_filter !== 'all') {
    $all_comments = array_filter($all_comments, function($comment) use ($status_filter) {
        return $comment['status'] === $status_filter;
    });
}

// Get counts
$pending_count = count(array_filter(get_all_comments(), function($c) { return $c['status'] === 'pending'; }));
$approved_count = count(array_filter(get_all_comments(), function($c) { return $c['status'] === 'approved'; }));
$rejected_count = count(array_filter(get_all_comments(), function($c) { return $c['status'] === 'rejected'; }));
$total_count = count(get_all_comments());

$page_title = 'Comments Management';
include 'includes/admin-header.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1>Comments Management</h1>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <h3><?php echo $total_count; ?></h3>
            <p>Total Comments</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $pending_count; ?></h3>
            <p>Pending</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $approved_count; ?></h3>
            <p>Approved</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $rejected_count; ?></h3>
            <p>Rejected</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="filter-row">
        <a href="?status=all" class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All (<?php echo $total_count; ?>)</a>
        <a href="?status=pending" class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending (<?php echo $pending_count; ?>)</a>
        <a href="?status=approved" class="filter-btn <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved (<?php echo $approved_count; ?>)</a>
        <a href="?status=rejected" class="filter-btn <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">Rejected (<?php echo $rejected_count; ?>)</a>
    </div>
    
    <!-- Bulk Actions -->
    <?php if (!empty($all_comments)): ?>
        <form method="POST" id="bulk-form">
            <div class="bulk-actions">
                <select name="bulk_action" id="bulk-action">
                    <option value="">Bulk Actions</option>
                    <option value="bulk_approve">Approve Selected</option>
                    <option value="bulk_delete">Delete Selected</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="applyBulkAction()">Apply</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="30"><input type="checkbox" id="select-all" onclick="toggleAll(this)"></th>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_comments as $comment): ?>
                        <tr>
                            <td><input type="checkbox" name="comment_ids[]" value="<?php echo $comment['id']; ?>" class="comment-checkbox"></td>
                            <td>
                                <strong><?php echo htmlspecialchars($comment['author_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($comment['author_email']); ?></small>
                            </td>
                            <td>
                                <?php echo nl2br(htmlspecialchars(substr($comment['content'], 0, 100))); ?>
                                <?php if (strlen($comment['content']) > 100): ?>...<?php endif; ?>
                                <?php if ($comment['parent_id'] > 0): ?>
                                    <br><small><em>Reply to comment #<?php echo $comment['parent_id']; ?></em></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $post = get_post($comment['post_id']);
                                if ($post): ?>
                                    <a href="../blog/<?php echo htmlspecialchars($post['slug']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                <?php else: ?>
                                    <em>Post deleted</em>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $comment['status']; ?>">
                                    <?php echo ucfirst($comment['status']); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <?php if ($comment['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-action btn-approve" title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-action btn-reject" title="Reject">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                <?php elseif ($comment['status'] === 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-action btn-reject" title="Reject">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                <?php elseif ($comment['status'] === 'rejected'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-action btn-approve" title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn-action btn-delete" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php else: ?>
        <div class="empty-state">
            <p>No comments found</p>
        </div>
    <?php endif; ?>
</div>

<style>
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.stat-box h3 {
    font-size: 32px;
    margin: 0 0 10px 0;
    color: #333;
}

.stat-box p {
    margin: 0;
    color: #666;
}

.filter-row {
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
}

.filter-btn {
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.filter-btn:hover {
    background: #f5f5f5;
}

.filter-btn.active {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}

.bulk-actions {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
}

.bulk-actions select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.btn-action {
    background: none;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.3s;
}

.btn-approve {
    color: #28a745;
}

.btn-approve:hover {
    background: #d4edda;
}

.btn-reject {
    color: #dc3545;
}

.btn-reject:hover {
    background: #f8d7da;
}

.btn-delete {
    color: #dc3545;
}

.btn-delete:hover {
    background: #f8d7da;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.empty-state p {
    color: #666;
    font-size: 18px;
}
</style>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function applyBulkAction() {
    const bulkAction = document.getElementById('bulk-action').value;
    const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked');
    
    if (!bulkAction) {
        alert('Please select a bulk action');
        return;
    }
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one comment');
        return;
    }
    
    if (bulkAction === 'bulk_delete') {
        if (!confirm('Are you sure you want to delete the selected comments?')) {
            return;
        }
    }
    
    const form = document.getElementById('bulk-form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'action';
    input.value = bulkAction;
    form.appendChild(input);
    form.submit();
}
</script>

<?php include 'includes/admin-footer.php'; ?>
