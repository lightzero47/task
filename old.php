<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

?>



<?php
// Database connection (assumed already created)
$sql = "
    SELECT 
        t.task_id,
        t.title,
        t.description,
        t.deadline,
        t.status,
        GROUP_CONCAT(DISTINCT u.name SEPARATOR ', ') AS assigned_users,
        GROUP_CONCAT(DISTINCT u.avatar_url SEPARATOR ',') AS avatar_urls,
        GROUP_CONCAT(DISTINCT CONCAT(u.name, ': ', tc.comment, ' [', DATE_FORMAT(tc.created_at, '%Y-%m-%d'), ']') SEPARATOR '<br>') AS comments
    FROM tasks t
    LEFT JOIN task_assignments ta ON t.task_id = ta.task_id
    LEFT JOIN users u ON ta.user_id = u.user_id
    LEFT JOIN task_comments tc ON t.task_id = tc.task_id AND tc.user_id = u.user_id
    GROUP BY t.task_id
    ORDER BY t.task_id DESC
";
$result = $conn->query($sql);
$serial = 1;
?>

<!-- Orders -->
<div class="orders">
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="box-title">Tasks</h4>
                </div>
                <div class="card-body--">
                    <div class="table-stats order-table ov-h">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th class="serial">#</th>
                                    <th class="avatar">Avatar</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Task</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()) {
                                    $avatars = explode(",", $row['avatar_urls'] ?? '');
                                    $assignedUsers = htmlspecialchars($row['assigned_users'] ?? 'N/A');
                                    $taskTitle = htmlspecialchars($row['title']);
                                    $deadline = !empty($row['deadline']) ? date('Y-m-d', strtotime($row['deadline'])) : 'N/A';
                                    $comments = $row['comments'] ?? 'No comments';
                                    $status = strtolower($row['status']);
                                ?>
                                <tr>
                                    <td class="serial"><?php echo $serial++ . '.'; ?></td>
                                    <td class="avatar">
                                        <div class="round-img">
                                            <a href="#">
                                                <img class="rounded-circle" 
                                                     src="<?php echo !empty($avatars[0]) ? $avatars[0] : 'images/avatar/default.jpg'; ?>" 
                                                     alt="avatar">
                                            </a>
                                        </div>
                                    </td>
                                    <td>#<?php echo $row['task_id']; ?></td>
                                    <td><span class="name"><?php echo $assignedUsers; ?></span></td>
                                    <td><span class="product"><?php echo $taskTitle; ?></span></td>
                                    <td><span class="count"><?php echo $deadline; ?></span></td>
                                    <td>
                                        <span class="badge badge-<?php echo $status; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <!-- Optional comments row -->
                                <tr>
                                    <td colspan="7">
                                        <strong>Comments:</strong><br>
                                        <?php echo $comments; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div> <!-- /.table-stats -->
                </div>
            </div> <!-- /.card -->
        </div>  <!-- /.col-xl-8 -->

        <div class="col-xl-4">
            <div class="row">
                <div class="col-lg-6 col-xl-12">
                    <div class="card br-0">
                        <div class="card-body">
                            <div class="chart-container ov-h">
                                <div id="flotPie1" class="float-chart"></div>
                            </div>
                        </div>
                    </div><!-- /.card -->
                </div>

                <div class="col-lg-6 col-xl-12">
                    <div class="card bg-flat-color-3">
                        <div class="card-body">
                            <h4 class="card-title m-0 white-color">August 2018</h4>
                        </div>
                        <div class="card-body">
                            <div id="flotLine5" class="flot-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.col-md-4 -->
    </div>
</div>







<?php

include('includes/footer.php');
?>
