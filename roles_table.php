<?php
include('includes/session.php'); 
include('includes/header.php'); 
include('includes/navbar.php'); 

// Fetch roles
$query = "SELECT role_id, role_name FROM roles";
$result = mysqli_query($conn, $query);
?>

<!-- Breadcrumb -->
<div class="breadcrumbs">
    <div class="page-title">
        <h1>Roles Table</h1>
    </div>
</div>

<div class="content">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Roles Table</strong>
                        <a href="role_form.php" class="btn btn-success btn-sm float-right">Add Role</a>
                    </div>
                    <div class="card-body">
                        <table id="bootstrap-data-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Role ID</th>
                                    <th>Role Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['role_id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                                        <td>
                                            <a href="role_form.php?id=<?php echo $row['role_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="role_delete.php?id=<?php echo $row['role_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?');">Delete</a>
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

<?php include("includes/footer.php") ?>
