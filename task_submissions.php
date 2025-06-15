<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

$isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;

// SQL to get submissions with task and user info
$sql = "
    SELECT 
        ts.submission_id,
        ts.task_id,
        ts.user_id,
        ts.text_content,
        ts.media_url,
        ts.submitted_at,
        t.title AS task_title,
        u.name AS user_name
    FROM task_submissions ts
    JOIN tasks t ON ts.task_id = t.task_id
    JOIN users u ON ts.user_id = u.user_id
    ORDER BY ts.submitted_at DESC
";

$result = $conn->query($sql);
$serial = 1;
?>

<!-- Submissions Table -->
<div class="orders">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="box-title">Task Submissions</h4>
                </div>
                <div class="card-body--">
                    <div class="table-stats order-table ov-h">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="serial">#</th>
                                    <th>Task</th>
                                    <th>Submitted By</th>
                                    <th>Text Content</th>
                                    <th>Media</th>
                                    <th>Submitted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="serial"><?php echo $serial++ . '.'; ?></td>
                                            <td>#<?php echo (int)$row['task_id'] . ' - ' . htmlspecialchars($row['task_title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($row['text_content'])); ?></td>
                                            <td>
                                                <?php if (!empty($row['media_url'])): ?>
                                                    <a href="<?php echo htmlspecialchars($row['media_url']); ?>" target="_blank">View Media</a>
                                                <?php else: ?>
                                                    No Media
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date("j M Y g:ia", strtotime($row['submitted_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">No submissions found.</td>
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

<?php include('includes/footer.php'); ?>
