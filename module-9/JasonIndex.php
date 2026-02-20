<?php
declare(strict_types=1);
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 9 
 * February 19 2026
 *
 * JasonIndex.php
 *
 * Purpose:
 *  - Index page that links to the Module 9 pages (Search + Add)
 *  - Also links to Module 8 files (Create / Drop / Populate / Query)
 */


/**
 * Module 9 dynamic index presentation
 * Determines whether martial_arts table exists on every page load.
 */

session_start();

$dbHost = "localhost";
$dbUser = "student1";
$dbPass = "pass";
$dbName = "baseball_01";
$tableName = "martial_arts";

/** Check if table exists (real state every refresh). */
function table_exists(string $host, string $user, string $pass, string $dbName, string $table): bool {
    $conn = mysqli_connect($host, $user, $pass, $dbName);
    if (!$conn) return false;

    $sql = "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=? LIMIT 1";
    $exists = false;
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $dbName, $table);
        if (mysqli_stmt_execute($stmt)) {
        $res = mysqli_stmt_get_result($stmt);
        $exists = ($res && mysqli_fetch_row($res));
        if ($res) mysqli_free_result($res);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return (bool)$exists;
}

/** Run a PHP script file and capture any echoed HTML/text. */
function run_script(string $file): string {
    ob_start();
    require $file;   // executes the script
    return ob_get_clean();
}

/* ---------- Handle button actions ---------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    // Map actions -> your Module 8 files (same directory)
    $map = [
        "create"   => "JasonCreateTable.php",
        "drop"     => "JasonDropTable.php",
        "populate" => "JasonPopulateTable.php",
        "query"    => "JasonQueryTable.php"
    ];

    if (isset($map[$action]) && is_file($map[$action])) {
        $_SESSION["last_action"] = $action;
        $_SESSION["action_output"] = run_script($map[$action]);
    } else {
        $_SESSION["last_action"] = "error";
        $_SESSION["action_output"] = "Script not found for action: " . htmlspecialchars($action);
    }

    // Redirect back to index (forces refresh + prevents form re-submit)
    header("Location: JasonIndex.php");
    exit;
}

$tableExists = table_exists($dbHost, $dbUser, $dbPass, $dbName, $tableName);
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jason - Module 9 Index</title>
    <link rel="stylesheet" href="JasonStyles.css">
</head>
<body>

<div class="card">
    <h1>Module 9 – Martial Arts Database</h1>
    <p class="meta">
        Database: <code>baseball_01</code> | Table: <code>martial_arts</code>
    </p>

    <?php if (!$tableExists): ?>
        <div class="section">
            <h2>Data Table Creation</h2>
                <p class="meta">Table <code>martial_arts</code> does not exist.</p>

            <form method="post" class="actions">
                <button type="submit" name="action" value="create">Create Table</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="section">
    <h2>Table Services</h2>
    <div class="nav">
      <a class="btn" href="JasonQuery.php">Search Records</a>
      <a class="btn" href="JasonForm.php">Add a Record</a>
    </div>
    </div>

    <?php if ($tableExists): ?>
    <div class="section">
        <h2>Data Table Reset</h2>
        <form method="post" class="actions">
        <button type="submit" name="action" value="populate">Populate Table</button>
        <button type="submit" name="action" value="drop"
                onclick="return confirm('Drop the table? This cannot be undone.');">
            Drop Table
        </button>
        </form>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Jason Luttrell - CSD 440 Server-Side Scripting</h2>
    <p class="meta">
        Module 9, February 19 2026
    </p>
</div>
  

</body>
</html>