<?php
// Include database connection
include 'DBconnect.php';

// Handle form submission for creating and updating
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle the image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "assets/images/"; // Directory where the images will be saved
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size (e.g., 5MB maximum)
        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if everything is ok to upload the file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file; // Save the path to the database
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Prepare and bind the SQL statements
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        // Update product
        $id = $_POST['product_id'];
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image_url=? WHERE id=?");
        $stmt->bind_param('ssdsi', $name, $description, $price, $image_url, $id);
    } else {
        // Create product
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssds', $name, $description, $price, $image_url);
    }

    // Execute the query
    if ($stmt->execute()) {
        echo "Product saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare and bind the delete statement
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        echo "Product deleted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch products for display
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Manage Products</h2>

        <!-- Create/Update Form -->
        <div class="card mb-5">
            <div class="card-header">Add/Edit Product</div>
            <div class="card-body">
                <form method="POST" action="">
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
                        <label for="image_url">Image URL</label>
                        <input type="file" class="form-control" id="image_url" name="image_url" required>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
                        <tr>
                            <td>' . htmlspecialchars($row["id"]) . '</td>
                            <td>' . htmlspecialchars($row["name"]) . '</td>
                            <td>' . htmlspecialchars($row["description"]) . '</td>
                            <td>$' . htmlspecialchars($row["price"]) . '</td>
                            <td><img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '" width="100"></td>
                            <td>
                                <button class="btn btn-info btn-edit" data-id="' . htmlspecialchars($row["id"]) . '" data-name="' . htmlspecialchars($row["name"]) . '" data-description="' . htmlspecialchars($row["description"]) . '" data-price="' . htmlspecialchars($row["price"]) . '" data-image="' . htmlspecialchars($row["image_url"]) . '">Edit</button>
                                <a href="?delete_id=' . htmlspecialchars($row["id"]) . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this product?\')">Delete</a>
                            </td>
                        </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="text-center">No products found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Handle the edit button click
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                const price = this.getAttribute('data-price');
                const image = this.getAttribute('data-image');

                document.getElementById('product_id').value = id;
                document.getElementById('name').value = name;
                document.getElementById('description').value = description;
                document.getElementById('price').value = price;
                document.getElementById('image_url').value = image;
            });
        });
    </script>

</body>

</html>