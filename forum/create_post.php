<?php
require_once 'config/database.php';
require_once 'classes/Post.php';
require_once 'includes/session.php';

requireLogin();

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $post = new Post($db);

    $post->user_id = getUserId();
    $post->title = $_POST['title'] ?? '';
    $post->content = $_POST['content'] ?? '';
    $post->image_path = null;

    // Handle image upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if(in_array(strtolower($filetype), $allowed)) {
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = $upload_dir . $new_filename;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $post->image_path = $upload_path;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if(empty($post->title) || empty($post->content)) {
        $error = "Title and content are required!";
    } elseif(empty($error)) {
        if($post->create()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to create post.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - Forum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="create-post-form">
            <h1>Create New Post</h1>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="6" required></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image (optional):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <div id="image-preview"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create Post</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>