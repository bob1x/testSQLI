<?php
// Include database connection
include 'DBconnect.php';

// Get category_id from GET parameters without sanitization (for SQL injection testing)
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// Fetch categories for the list at the top
$categorySql = "SELECT * FROM categories";
$categoriesResult = $conn->query($categorySql);

// Build the main product SQL query
$productSql = "SELECT * FROM products";
if (!empty($category_id)) {
    $productSql .= " WHERE cat_id = $category_id";
}

$productsResult = $conn->query($productSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Store </title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="Index.php">Product Store</a>
        <a class="" href="manage_products.php">Products</a>
    </nav>

    <!-- Categories Row -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Categories</h2>
        <div class="row">
            <?php
            if ($categoriesResult && $categoriesResult->num_rows > 0) {
                while ($category = $categoriesResult->fetch_assoc()) {
                    echo '
                    <div class="col-md-2 text-center mb-3">
                        <a href="?category_id=' . $category["id"] . '" class="btn btn-secondary">' . htmlspecialchars($category["name"]) . '</a>
                    </div>';
                }
            } else {
                echo '<p class="text-center">No categories found.</p>';
            }
            ?>
        </div>

        <!-- Products List -->
        <h2 class="text-center mt-4 mb-4">Products</h2>
        <div class="row">
            <?php
            if ($productsResult && $productsResult->num_rows > 0) {
                while ($product = $productsResult->fetch_assoc()) {
                    echo '
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img class="card-img-top" src="' . htmlspecialchars($product["image_url"]) . '" alt="' . htmlspecialchars($product["name"]) . '">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($product["name"]) . '</h5>
                                <p class="card-text">' . htmlspecialchars($product["description"]) . '</p>
                                <p class="card-text"><strong>$' . htmlspecialchars($product["price"]) . '</strong></p>
                                <a href="#" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p class="text-center">No products found in this category.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>