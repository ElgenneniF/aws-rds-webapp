<?php include "../inc/dbinfo.inc"; ?>

<html>
<head>
  <title>Products CRUD</title>
</head>
<body>
<h1>Products Management</h1>

<?php
/* Conectar no banco */
$constring = "host=" . DB_SERVER . " dbname=" . DB_DATABASE . " user=" . DB_USERNAME . " password=" . DB_PASSWORD ;
$connection = pg_connect($constring);

if (!$connection){
  echo "❌ Failed to connect to PostgreSQL";
  exit;
}

/* Criar produto */
if (isset($_POST['action']) && $_POST['action'] == "create") {
  $name = pg_escape_string($_POST['name']);
  $price = (float) $_POST['price'];
  $query = "INSERT INTO PRODUCTS (name, price) VALUES ('$name', $price);";
  pg_query($connection, $query);
}

/* Atualizar produto */
if (isset($_POST['action']) && $_POST['action'] == "update") {
  $id = (int) $_POST['id'];
  $name = pg_escape_string($_POST['name']);
  $price = (float) $_POST['price'];
  $query = "UPDATE PRODUCTS SET name='$name', price=$price WHERE id=$id;";
  pg_query($connection, $query);
}

/* Deletar produto */
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $query = "DELETE FROM PRODUCTS WHERE id=$id;";
  pg_query($connection, $query);
}
?>

<!-- Formulário de criação -->
<h2>Add Product</h2>
<form action="Products.php" method="POST">
  <input type="hidden" name="action" value="create">
  Name: <input type="text" name="name" required>
  Price: <input type="number" step="0.01" name="price" required>
  <input type="submit" value="Add">
</form>

<!-- Listagem -->
<h2>Product List</h2>
<table border="1" cellpadding="5">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Created At</th>
    <th>Actions</th>
  </tr>

<?php
$result = pg_query($connection, "SELECT * FROM PRODUCTS ORDER BY id DESC;");
while($row = pg_fetch_assoc($result)) {
  echo "<tr>";
  echo "<td>" . $row['id'] . "</td>";
  echo "<td>" . htmlspecialchars($row['name']) . "</td>";
  echo "<td>" . $row['price'] . "</td>";
  echo "<td>" . $row['created_at'] . "</td>";
  echo "<td>
          <a href='Products.php?edit=" . $row['id'] . "'>Edit</a> | 
          <a href='Products.php?delete=" . $row['id'] . "' onclick=\"return confirm('Delete?');\">Delete</a>
        </td>";
  echo "</tr>";
}
pg_free_result($result);
?>
</table>

<?php
/* Formulário de edição */
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $result = pg_query($connection, "SELECT * FROM PRODUCTS WHERE id=$id;");
  if ($prod = pg_fetch_assoc($result)) {
    echo "<h2>Edit Product</h2>";
    echo "<form action='Products.php' method='POST'>
            <input type='hidden' name='action' value='update'>
            <input type='hidden' name='id' value='".$prod['id']."'>
            Name: <input type='text' name='name' value='".htmlspecialchars($prod['name'])."' required>
            Price: <input type='number' step='0.01' name='price' value='".$prod['price']."' required>
            <input type='submit' value='Update'>
          </form>";
  }
}
pg_close($connection);
?>

</body>
</html>
