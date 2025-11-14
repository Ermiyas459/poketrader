<title>Dashboard | ADMIN</title>

<?php
if (!isset($_SESSION['role']) || $_SESSION ['role'] == "Klant") {
    unset($_SESSION["role"]);
    header("Location: index.php?page=404");
    exit;
}

include '../Private/poketrader_db.connection.php';



$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE LOWER(role) = 'admin'")->fetchColumn();


$roleFilter = $_GET['role'] ?? '';
$search = $_GET['search'] ?? '';


$sql = "SELECT userId, email, name, role FROM users WHERE 1=1";
$params = [];


if (!empty($roleFilter)) {
    $sql .= " AND LOWER(role) = LOWER(:role)";
    $params[':role'] = strtolower($roleFilter);
}


if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-dashboard">

    <h1>Admin Dashboard</h1>


    <div class="stats-cards">
        <div class="card">
            <h2>👥</h2>
            <p>Total Users</p>
            <span><?php echo $totalUsers; ?></span>
        </div>
        <div class="card">
            <h2>🛡️</h2>
            <p>Admins</p>
            <span><?php echo $totalAdmins; ?></span>
        </div>
    </div>


    <div class="user-controls">
        <form method="GET" action="">
            <input type="hidden" name="page" value="admindashboard">

            <input type="text" name="search" placeholder="Search users..."
                   value="<?php echo htmlspecialchars($search); ?>">

            <select name="role">
                <option value="" <?php if ($roleFilter === '') echo 'selected'; ?>>All Roles</option>
                <option value="Klant" <?php if ($roleFilter === 'Klant') echo 'selected'; ?>>Klant</option>
                <option value="Admin" <?php if ($roleFilter === 'Admin') echo 'selected'; ?>>Admin</option>
            </select>

            <button type="submit">Filter</button>
        </form>
        <button  class="btn add"><a href="index.php?page=registration">+ Add User</a></button>
    </div>


    <div class="user-table">
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <div class="user-row">
                    <div class="user-info">
                        <div class="user-pic">
                            <img src="images/default.png" alt="User Pic">
                        </div>
                        <div>
                            <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span><br>
                            <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                    </div>
                    <div class="user-role">
                        <?php echo htmlspecialchars($user['role']); ?>
                    </div>
                    <div class="user-actions">
                        <a href="php/deleteuser.php?id=<?php echo $user['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this user?');">
                            Delete
                        </a>
                        <a href="index.php?page=edituser&id<?php echo $user['id']; ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

