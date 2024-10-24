<?php
// Include database connection
include 'DBconnect.php';

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Items and Products Page</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Product Store</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Carousel -->
    <div id="productCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="https://via.placeholder.com/1200x400" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Featured Product 1</h5>
                    <p>Best deal of the season!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="https://via.placeholder.com/1200x400" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Featured Product 2</h5>
                    <p>Limited time offer!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="https://via.placeholder.com/1200x400" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Featured Product 3</h5>
                    <p>New arrivals!</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Product Cards Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Our Products</h2>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-md-4">
                        <div class="card">
                            <img class="card-img-top" src="' . $row["image_url"] . '" alt="' . $row["name"] . '">
                            <div class="card-body">
                                <h5 class="card-title">' . $row["name"] . '</h5>
                                <p class="card-text">' . $row["description"] . '</p>
                                <p class="card-text"><strong>$' . $row["price"] . '</strong></p>
                                <a href="#" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>No products found.</p>';
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