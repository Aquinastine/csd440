<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 9
 * February 19 2026
 *
 * JasonForm.php
 *
 * Purpose:
 *  - Form page to add a record to the martial_arts table (MySQLi prepared statements)
 */

$style = "";
$origin = "";
$age = "";
$parent = "";
$founder = "";

$okMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $style = trim($_POST["style"] ?? "");
  $origin = trim($_POST["origin"] ?? "");
  $age = trim($_POST["age"] ?? "");
  $parent = trim($_POST["parent"] ?? "");
  $founder = trim($_POST["founder"] ?? "");

  if ($style === "" || $origin === "" || $founder === "") {
    $errorMsg = "Style, Origin, and Founder are required.";
  } elseif ($age !== "" && !ctype_digit($age)) {
    $errorMsg = "Age must be a whole number (or leave it blank).";
  } else {
    $conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");
    if (!$conn) {
      $errorMsg = "Connection failed: " . mysqli_connect_error();
    } else {
      $sql = "INSERT INTO martial_arts (style, origin, age, parent, founder) VALUES (?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $sql);

      if (!$stmt) {
        $errorMsg = "Prepare failed: " . mysqli_error($conn);
      } else {
        // allow NULL age if blank
        $ageVal = ($age === "") ? null : (int)$age;

        mysqli_stmt_bind_param($stmt, "ssiss", $style, $origin, $ageVal, $parent, $founder);

        if (!mysqli_stmt_execute($stmt)) {
          $errorMsg = "Insert failed: " . mysqli_stmt_error($stmt);
        } else {
          $okMsg = "Record added successfully!";
          $style = $origin = $age = $parent = $founder = "";
        }

        mysqli_stmt_close($stmt);
      }

      mysqli_close($conn);
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jason - Add Record</title>
  <link rel="stylesheet" href="JasonStyles.css">
</head>
<body>

<div class="card">
  <h1>Add a Martial Arts Record</h1>
  <p class="meta">Insert into <code>martial_arts</code> (database: <code>baseball_01</code>).</p>

  <div class="nav">
    <a class="btn" href="JasonIndex.php">Back to Index</a>
    <a class="btn" href="JasonQuery.php">Search Records</a>
  </div>

  <?php if ($okMsg !== ""): ?>
    <div class="ok"><?php echo htmlspecialchars($okMsg); ?></div>
  <?php endif; ?>

  <?php if ($errorMsg !== ""): ?>
    <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
  <?php endif; ?>

  <div class="section">
    <h2>Record Details</h2>

    <form method="post" action="JasonForm.php">
      <div class="row">
        <div class="field">
          <label for="style">Style *</label>
          <input type="text" id="style" name="style" value="<?php echo htmlspecialchars($style); ?>" required>
        </div>

        <div class="field">
          <label for="origin">Origin *</label>
          <input type="text" id="origin" name="origin" value="<?php echo htmlspecialchars($origin); ?>" required>
        </div>

        <div class="field">
          <label for="age">Age (years)</label>
          <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" min="0">
        </div>
      </div>

      <div class="row">
        <div class="field">
          <label for="parent">Parent (optional)</label>
          <input type="text" id="parent" name="parent" value="<?php echo htmlspecialchars($parent); ?>">
        </div>

        <div class="field">
          <label for="founder">Founder *</label>
          <input type="text" id="founder" name="founder" value="<?php echo htmlspecialchars($founder); ?>" required>
        </div>
      </div>

      <div class="actions">
        <button type="submit">Add Record</button>
        <button class="submit" type="button" onclick="window.location='JasonForm.php'">Clear</button>
      </div>
    </form>
  </div>

  <div class="footer">
    After adding a record, go to <a href="JasonQuery.php">Search</a> to verify it appears.
  </div>
</div>
<div class="card">
  <h2>Jason Luttrell - CSD 440 Server-Side Scripting</h2>
  <p class="meta">
    Module 9, February 19 2026
  </p>
</div>
</body>
</html>