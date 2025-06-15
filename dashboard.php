<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

// Get the role of the current logged-in user
$userRole = $_SESSION['role_id'];  // Assuming you set 'role_id' in session

// Query to get the total counts for each table
$totalUsersQuery = "SELECT COUNT(*) FROM users";
$totalRolesQuery = "SELECT COUNT(*) FROM roles";
$totalTasksQuery = "SELECT COUNT(*) FROM tasks";
$totalTaskAssignmentsQuery = "SELECT COUNT(*) FROM task_assignments";
$totalTaskSubmissionsQuery = "SELECT COUNT(*) FROM task_submissions";
$totalTaskCommentsQuery = "SELECT COUNT(*) FROM task_comments";

// Query to get task breakdown based on status
$taskStatusQuery = "SELECT status, COUNT(*) FROM tasks GROUP BY status";
$adminStatusQuery = "SELECT admin_status, COUNT(*) FROM tasks WHERE admin_status IS NOT NULL GROUP BY admin_status";

// Execute queries
$totalUsers = $conn->query($totalUsersQuery)->fetch_row()[0];
$totalRoles = $conn->query($totalRolesQuery)->fetch_row()[0];
$totalTasks = $conn->query($totalTasksQuery)->fetch_row()[0];
$totalTaskAssignments = $conn->query($totalTaskAssignmentsQuery)->fetch_row()[0];
$totalTaskSubmissions = $conn->query($totalTaskSubmissionsQuery)->fetch_row()[0];
$totalTaskComments = $conn->query($totalTaskCommentsQuery)->fetch_row()[0];

// Fetch task status breakdown
$taskStatuses = $conn->query($taskStatusQuery)->fetch_all(MYSQLI_ASSOC);

// Fetch admin status breakdown (only for admin)
$adminStatuses = [];
if ($userRole == 1) {  // Admin role
    $adminStatuses = $conn->query($adminStatusQuery)->fetch_all(MYSQLI_ASSOC);
}
?>

    <!-- Sidebar and Header (same as before) -->

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="my-4">Dashboard</h2>

                <!-- Admin check -->
                <?php if ($userRole == 1): ?>
                    <!-- Admin View - Displaying counts for all tables -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <p><?php echo $totalUsers; ?> Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Roles</h5>
                                    <p><?php echo $totalRoles; ?> Roles</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Tasks</h5>
                                    <p><?php echo $totalTasks; ?> Tasks</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Status Breakdown -->
                    <div class="row">
                        <?php foreach ($taskStatuses as $status): ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo ucfirst($status['status']); ?> Status</h5>
                                        <p><?php echo $status['COUNT(*)']; ?> tasks</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Admin Status Breakdown -->
                    <div class="row">
                        <?php foreach ($adminStatuses as $adminStatus): ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo ucfirst($adminStatus['admin_status']); ?> Admin Status</h5>
                                        <p><?php echo $adminStatus['COUNT(*)']; ?> tasks</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Task Assignments and Submissions -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Task Assignments</h5>
                                    <p><?php echo $totalTaskAssignments; ?> Assignments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Task Submissions</h5>
                                    <p><?php echo $totalTaskSubmissions; ?> Submissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Task Comments</h5>
                                    <p><?php echo $totalTaskComments; ?> Comments</p>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Non-admin (user) View - Displaying only task-related counts -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Tasks</h5>
                                    <p><?php echo $totalTasks; ?> Tasks</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Task Submissions</h5>
                                    <p><?php echo $totalTaskSubmissions; ?> Submissions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Task Comments</h5>
                                    <p><?php echo $totalTaskComments; ?> Comments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>



<?php

include('includes/footer.php');
?>
