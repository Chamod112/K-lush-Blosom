<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    // Validate inputs
    if (!$name) {
        $errors[] = "Product name is required.";
    }
    if ($price <= 0) {
        $errors[] = "Price must be positive.";
    }

    // Handle image upload
    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg','image/png','image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . "." . $ext;
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name);
        } else {
            $errors[] = "Only JPG, PNG and GIF images are allowed.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $image_name);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Failed to add product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product - Admin - K Lush Blosom</title>
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
</style>
</head>
<body>

<header>
  <h1>Add Product</h1>
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

  <form action="" method="post" enctype="multipart/form-data" novalidate>
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4" placeholder="Write product details..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

    <label for="price">Price (LKR)</label>
    <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">

    <label for="image">Product Image (jpg, png, gif)</label>
    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">

    <button type="submit">Add Product</button>
  </form>
</main>

</body>
</html>
