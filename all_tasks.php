<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;

// Updated SQL Query to fetch admin_status
$sql = "
    SELECT 
        t.task_id,
        t.title,
        t.description,
        t.deadline AS task_deadline,
        t.status AS task_status,
        t.admin_status,
        GROUP_CONCAT(DISTINCT u.name SEPARATOR ', ') AS assigned_users,
        GROUP_CONCAT(DISTINCT CONCAT(u.name, ': ', tc.comment, ' [', DATE_FORMAT(tc.created_at, '%Y-%m-%d'), ']') SEPARATOR '<br>') AS comments
    FROM tasks t
    LEFT JOIN task_assignments ta ON t.task_id = ta.task_id
    LEFT JOIN users u ON ta.user_id = u.user_id
    LEFT JOIN task_comments tc ON t.task_id = tc.task_id
    WHERE t.is_active = 1
    GROUP BY t.task_id
    ORDER BY t.task_id DESC
";

$result = $conn->query($sql);
$serial = 1;

// Status badge function
function getStatusBadgeClass($status) {
    return match(strtolower($status)) {
        'not_started'   => 'secondary',
        'ongoing'       => 'warning',
        'completed'     => 'success',
        'verified'      => 'info',
        'not_accepted'  => 'danger',
        default         => 'primary',
    };
}
?>

<!-- Tasks Table -->
<div class="orders">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="box-title">Tasks</h4>

                    <!-- Filter Buttons -->
                    <div class="mb-3">
                        <button class="btn btn-outline-dark btn-sm status-filter" data-status="all">All</button>
                        <button class="btn btn-outline-secondary btn-sm status-filter" data-status="not_started">Not Started</button>
                        <button class="btn btn-outline-warning btn-sm status-filter" data-status="ongoing">Ongoing</button>
                        <button class="btn btn-outline-success btn-sm status-filter" data-status="completed">Completed</button>
                    </div>
                </div>

                <div class="card-body--">
                    <div class="table-stats order-table ov-h">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="serial">#</th>
                                    <th>ID</th>
                                    <th>Assigned Users</th>
                                    <th>Task & Comments</th>
                                    <th>Deadline</th>
                                    <th>Status<?= $isAdmin ? ' (Admin)' : '' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <?php
                                            $assignedUsers = htmlspecialchars($row['assigned_users'] ?? 'Unassigned');
                                            $taskTitle     = htmlspecialchars($row['title']);
                                            $comments      = $row['comments'] ?: 'No comments';
                                            $commentsSafe  = htmlspecialchars_decode($comments);
                                            $deadline      = date("j M Y g:ia", strtotime($row['task_deadline']));
                                            $status        = htmlspecialchars($row['task_status'] ?? 'unknown');
                                            $adminStatus   = htmlspecialchars($row['admin_status'] ?? '');

                                            $badgeClass        = getStatusBadgeClass($status);
                                            $adminBadgeClass   = $adminStatus ? getStatusBadgeClass($adminStatus) : '';
                                        ?>
                                        <tr data-status="<?= strtolower($status); ?>">
                                            <td class="serial"><?php echo $serial++ . '.'; ?></td>
                                            <td>#<?php echo (int)$row['task_id']; ?></td>
                                            <td><span class="name"><?php echo $assignedUsers; ?></span></td>
                                            <td>
                                                <span class="product"><?php echo $taskTitle; ?></span><br>
                                                <div style="margin-bottom: 8px;">
                                                    <small><?php echo $commentsSafe; ?></small>
                                                </div>

                                                <?php if ($isAdmin): ?>
                                                    <!-- Comment Form for Admin -->
                                                    <form method="POST" action="add_comment.php" class="d-flex" style="gap: 5px;">
                                                        <input type="hidden" name="task_id" value="<?php echo (int)$row['task_id']; ?>">
                                                        <input type="text" name="comment" class="form-control form-control-sm" placeholder="Add comment..." required>
                                                        <button type="submit" class="btn btn-sm btn-success">Post</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>

                                            <td><span><?php echo $deadline; ?></span></td>

                                            <td>
                                                <form method="POST" action="update_task_status.php" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <input type="hidden" name="task_id" value="<?php echo (int)$row['task_id']; ?>">

                                                    <!-- General Status -->
                                                    <div class="d-flex" style="gap: 5px;">
                                                        <select name="status" class="form-control form-control-sm">
                                                            <?php
                                                            $statuses = ['not_started', 'ongoing', 'completed'];
                                                            foreach ($statuses as $s):
                                                                $selected = ($status === $s) ? 'selected' : '';
                                                                echo "<option value=\"$s\" $selected>" . ucfirst(str_replace('_', ' ', $s)) . "</option>";
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <!-- Admin Status -->
                                                    <?php if ($isAdmin): ?>
                                                        <div class="d-flex" style="gap: 5px;">
                                                            <select name="admin_status" class="form-control form-control-sm">
                                                                <option value="">-- Admin Review --</option>
                                                                <?php
                                                                $adminStatuses = ['verified', 'not_accepted'];
                                                                foreach ($adminStatuses as $as):
                                                                    $selected = ($adminStatus === $as) ? 'selected' : '';
                                                                    echo "<option value=\"$as\" $selected>" . ucfirst(str_replace('_', ' ', $as)) . "</option>";
                                                                endforeach;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    <?php endif; ?>

                                                    <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                                </form>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">No tasks found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div> <!-- /.table-stats -->
                </div>
            </div> <!-- /.card -->
        </div>  <!-- /.col-xl-12 -->
    </div>
</div>

<!-- JavaScript to filter rows -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.status-filter');
    const rows = document.querySelectorAll('tbody tr');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const status = btn.getAttribute('data-status');

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include('includes/footer.php'); ?>
