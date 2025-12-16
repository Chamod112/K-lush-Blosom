<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    // Delete image file if exists
    if ($product && $product['image'] && file_exists("../uploads/" . $product['image'])) {
        unlink("../uploads/" . $product['image']);
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit();
}

// Fetch products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - K Lush Blosom</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #fef6f9;
    margin: 0; padding: 0;
  }
  header {
    background: #b83b5e;
    color: white;
    padding: 1rem 2rem;
    display: flex; justify-content: space-between; align-items: center;
  }
  header h1 {
    margin: 0;
  }
  header a {
    color: white;
    text-decoration: none;
    font-weight: 600;
  }
  main {
    max-width: 900px;
    margin: 2rem auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(231, 84, 128, 0.15);
    padding: 2rem;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
  }
  th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #f8cdda;
    text-align: left;
  }
  th {
    background: #f7d7e3;
    color: #b83b5e;
  }
  tr:hover {
    background-color: #fff0f5;
  }
  img.product-img {
    max-width: 80px;
    border-radius: 8px;
  }
  a.button, button {
    background-color: #b83b5e;
    color: white;
    padding: 8px 15px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  a.button:hover, button:hover {
    background-color: #e75480;
  }
  .actions a {
    margin-right: 8px;
  }
  .bottom-button {
    text-align: center;
    margin-top: 30px;
  }
</style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <a href="logout.php">Logout</a>
</header>

<main>
  <a href="add_product.php" class="button" style="margin-bottom:1rem; display:inline-block;">+ Add New Product</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price (LKR)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td>
            <?php if ($row['image'] && file_exists("../uploads/" . $row['image'])): ?>
              <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-img">
            <?php else: ?>
              No Image
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
          <td><?= number_format($row['price'], 2) ?></td>
          <td class="actions">
            <a href="edit_product.php?id=<?= $row['id'] ?>" class="button">Edit</a>
            <a href="dashboard.php?delete=<?= $row['id'] ?>" class="button" style="background:#d9534f;" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div class="bottom-button">
    <a href="../product.html" class="button" style="padding: 12px 25px; border-radius: 25px; font-size: 1.1rem;">
      View Product Page
    </a>
  </div>
</main>

</body>
</html>
