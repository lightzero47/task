<?php

include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

// Fetch users with roles
$query = "SELECT u.user_id, u.name, u.email, u.username, r.role_name, u.is_active, u.created_at 
          FROM users u 
          LEFT JOIN roles r ON u.role_id = r.role_id";
$result = mysqli_query($conn, $query);
?>

        <!-- Breadcrumb -->
        <div class="breadcrumbs">
            <div class="page-title">
                <h1>Users table</h1>
            </div>
        </div>

        <div class="content">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Users Table</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $row['user_id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                                                <td><?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                                <td><?php echo $row['created_at']; ?></td>
                                                <td>
                                                    <a href="user_form.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="user_delete.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

        <?php include("includes/footer.php")  ?>