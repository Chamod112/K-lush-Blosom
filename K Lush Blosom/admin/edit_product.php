<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: dashboard.php");
    exit();
}

// Fetch existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if (!$name) {
        $errors[] = "Product name is required.";
    }
    if ($price <= 0) {
        $errors[] = "Price must be positive.";
    }

    $image_name = $product['image']; // Keep current image by default

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . "." . $ext;
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name)) {
                $errors[] = "Failed to upload new image.";
            } else {
                // Optionally delete old image file if exists and different from new one
                if ($product['image'] && file_exists($target_dir . $product['image'])) {
                    unlink($target_dir . $product['image']);
                }
            }
        } else {
            $errors[] = "Only JPG, PNG and GIF images are allowed.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_name, $id);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to update product.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Product - Admin - K Lush Blosom</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap');

  body {
    font-family: 'Poppins', sans-serif;
    background: #fef6f9;
    margin: 0;
    padding: 0;
  }
  header {
    background: #b83b5e;
    color: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  header h1 {
    margin: 0;
    font-weight: 500;
  }
  header a {
    color: white;
    text-decoration: none;
    font-weight: 600;
  }
  main {
    max-width: 480px;
    margin: 2rem auto;
    padding: 0 1rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(231, 84, 128, 0.15);
    padding: 2rem;
  }
  h2 {
    color: #b83b5e;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #555;
  }
  form input[type="text"],
  form input[type="number"],
  form textarea,
  form input[type="file"] {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #f8cdda;
    border-radius: 12px;
    font-size: 1rem;
    margin-bottom: 16px;
    transition: 0.3s ease;
    outline: none;
    color: #444;
    resize: vertical;
  }
  form input[type="text"]:focus,
  form input[type="number"]:focus,
  form textarea:focus,
  form input[type="file"]:focus {
    border-color: #b83b5e;
    box-shadow: 0 0 8px rgba(184, 59, 94, 0.5);
  }
  button {
    width: 100%;
    background: #b83b5e;
    border: none;
    border-radius: 14px;
    padding: 14px 0;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(184, 59, 94, 0.6);
    transition: background 0.3s ease;
  }
  button:hover {
    background: #e75480;
    box-shadow: 0 8px 20px rgba(231, 84, 128, 0.7);
  }
  .errors {
    background: #ffdde0;
    color: #b83b5e;
    padding: 12px 15px;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-weight: 600;
  }
  img.product-img {
    max-width: 140px;
    display: block;
    margin: 0 auto 20px auto;
    border-radius: 12px;
    box-shadow: 0 3px 6px rgba(184, 59, 94, 0.3);
  }
</style>
</head>
<body>

<header>
  <h1>Edit Product</h1>
  <a href="dashboard.php" style="color:white;">&larr; Back to Dashboard</a>
</header>

<main>
  <?php if (!empty($errors)) : ?>
    <div class="errors">
      <ul>
        <?php foreach($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($product['image'] && file_exists("../uploads/" . $product['image'])): ?>
    <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="Current Image" class="product-img">
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data" novalidate>
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? $product['name']) ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? $product['description']) ?></textarea>

    <label for="price">Price (LKR)</label>
    <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>">

    <label for="image">Replace Image (optional)</label>
    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">

    <button type="submit">Update Product</button>
  </form>
</main>

</body>
</html>
