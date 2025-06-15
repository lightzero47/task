<?php
include('includes/session.php');  // Start the session to access user details
include('includes/database.php'); // Include the database connection

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve task_id and comment from the form submission
    $task_id = intval($_POST['task_id']);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? null; // Get the logged-in user's ID from session

    // Validate user ID and comment
    // if (!$user_id || !$comment) {
    //     die("Invalid comment or user.");
    // }

    // Prepare and execute the database query to insert the comment
    $stmt = $conn->prepare("INSERT INTO task_comments (task_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $task_id, $user_id, $comment);

    // Execute the query and handle success/failure
    if ($stmt->execute()) {
        // Redirect back to the tasks page after successfully adding the comment
        header("Location: all_tasks.php"); 
        exit;
    } else {
        // Output error message if comment addition failed
        echo "Error adding comment: " . $conn->error;
    }

    // Close the prepared statement
    $stmt->close();
}
?>
