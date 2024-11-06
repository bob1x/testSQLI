<?php
// Include database connection
include 'DBconnect.php';

// Handle form submission for products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'save_product') {
    // Get inputs for products
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $cat_id = $_POST['cat_id'];

    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_url = $target_file;
    }

    // SQL for product creation or update
    if (!empty($_POST['product_id'])) {
        $id = $_POST['product_id'];
        $sql = "UPDATE products SET name='$name', description='$description', price=$price, image_url='$image_url', cat_id='$cat_id' WHERE id=$id";
    } else {
        $sql = "INSERT INTO products (name, description, price, image_url, cat_id) VALUES ('$name', '$description', $price, '$image_url', '$cat_id')";
    }

    // Execute the query
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}

// Handle category submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'save_category') {
    $category_name = $_POST['category_name'];
    $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}

// Fetch products
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

// Fetch categories
$category_sql = "SELECT * FROM categories";
$category_result = $conn->query($category_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products and Categories</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="Index.php">Product Store</a>
        <a class="" href="manage_products.php">Products</a>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Manage Products and Categories</h2>

        <!-- Category Form -->
        <div class="card mb-5">
            <div class="card-header">Add Category</div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="save_category">
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </form>
            </div>
        </div>

        <!-- Product Form -->
        <div class="card mb-5">
            <div class="card-header">Add/Edit Product</div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_product">
                    <input type="hidden" name="product_id" id="product_id">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="cat_id">Category</label>
                        <select class="form-control" id="cat_id" name="cat_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = $category_result->fetch_assoc()) : ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </form>
            </div>
        </div>

        <!-- Products List -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($product_result->num_rows > 0): ?>
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td><?= htmlspecialchars($row["name"]) ?></td>
                                <td><?= htmlspecialchars($row["description"]) ?></td>
                                <td>$<?= $row["price"] ?></td>
                                <td><img src="<?= htmlspecialchars($row["image_url"]) ?>" alt="<?= htmlspecialchars($row["name"]) ?>" width="100"></td>
                                <td><?= htmlspecialchars($row["cat_id"]) ?></td>
                                <td>
                                    <button class="btn btn-info btn-edit" data-id="<?= $row["id"] ?>" data-name="<?= htmlspecialchars($row["name"]) ?>" data-description="<?= htmlspecialchars($row["description"]) ?>" data-price="<?= $row["price"] ?>" data-image="<?= htmlspecialchars($row["image_url"]) ?>">Edit</button>
                                    <a href="?delete_id=<?= $row["id"] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>