<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Category</h2>
        <form action="/admin/categories/update.php" method="POST">
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter category name" required>
            </div>
            <div class="form-group">
                <label for="categoryDescription">Category Description</label>
                <textarea class="form-control" id="categoryDescription" name="categoryDescription" rows="3" placeholder="Enter category description" required></textarea>
            </div>
            <input type="hidden" name="categoryId" value="<?php echo $_GET['id']; ?>">
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
