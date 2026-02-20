<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 9
 * February, 19 2026
 *
 * JasonQuery.php
 *
 * Purpose:
 *  - Search the martial_arts table using user input (MySQLi prepared statements)
 *  - Display results as formatted “cards”
 */

$conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

/*
  We do NOT run a query by default.
  The page only queries when:
   - user clicks "Search" (with any filters), OR
   - user clicks "Display All Records"
*/
$style   = trim($_GET["style"] ?? "");
$origin  = trim($_GET["origin"] ?? "");
$founder = trim($_GET["founder"] ?? "");

// Button-driven intent
$doSearch = isset($_GET["doSearch"]);
$showAll  = isset($_GET["showAll"]);

$rows = [];
$errorMsg = "";
$didQuery = false;

if ($doSearch || $showAll) {
  $didQuery = true;

  $where = [];
  $params = [];
  $types = "";

  // If "Display All" was clicked, ignore filters and select everything
  if (!$showAll) {
    if ($style !== "") {
      $where[] = "style LIKE ?";
      $params[] = "%{$style}%";
      $types .= "s";
    }
    if ($origin !== "") {
      $where[] = "origin LIKE ?";
      $params[] = "%{$origin}%";
      $types .= "s";
    }
    if ($founder !== "") {
      $where[] = "founder LIKE ?";
      $params[] = "%{$founder}%";
      $types .= "s";
    }
  }

  $sql = "SELECT id, style, origin, age, parent, founder FROM martial_arts";
  if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
  }
  $sql .= " ORDER BY style";

  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    $errorMsg = "Prepare failed: " . mysqli_error($conn);
  } else {
    if (count($params) > 0) {
      mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (!mysqli_stmt_execute($stmt)) {
      $errorMsg = "Execute failed: " . mysqli_stmt_error($stmt);
    } else {
      $result = mysqli_stmt_get_result($stmt);
      if ($result) {
        while ($r = mysqli_fetch_assoc($result)) {
          $rows[] = $r;
        }
        mysqli_free_result($result);
      } else {
        $errorMsg = "Result retrieval failed: " . mysqli_stmt_error($stmt);
      }
    }
    mysqli_stmt_close($stmt);
  }
}

mysqli_close($conn);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jason - Search</title>
  <link rel="stylesheet" href="JasonStyles.css">
</head>
<body>

<div class="card">
  <h1>Search Martial Arts Records</h1>
  <p class="meta">
    Search the <code>martial_arts</code> table using any combination of fields.
    This page does not run a query until you click <strong>Search</strong> or <strong>Display All Records</strong>.
  </p>

  <div class="nav">
    <a class="btn" href="JasonIndex.php">Back to Index</a>
    <a class="btn" href="JasonForm.php">Add a Record</a>
  </div>

  <div class="section">
    <h2>Search Filters</h2>

    <form method="get" action="JasonQuery.php">
      <div class="row">
        <div class="field">
          <label for="style">Style</label>
          <input type="text" id="style" name="style" value="<?php echo htmlspecialchars($style); ?>" placeholder="e.g., Judo">
        </div>

        <div class="field">
          <label for="origin">Origin</label>
          <input type="text" id="origin" name="origin" value="<?php echo htmlspecialchars($origin); ?>" placeholder="e.g., Japan">
        </div>

        <div class="field">
          <label for="founder">Founder</label>
          <input type="text" id="founder" name="founder" value="<?php echo htmlspecialchars($founder); ?>" placeholder="e.g., Jigoro Kano">
        </div>
      </div>

      <div class="actions">
        <!-- Search (uses filters) -->
        <button type="submit" name="doSearch" value="1">Search</button>

        <!-- Reset -->
        <button class="submit" type="button" onclick="window.location='JasonQuery.php'">Reset</button>
      </div>
        <p></p>
       <!-- Display All (ignores filters) -->
        <button type="submit" name="showAll" value="1">Display All Records</button>

    </form>

    <?php if ($errorMsg !== ""): ?>
      <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>
  </div>

  <div class="section">
    <h2>Results</h2>

    <?php if (!$didQuery): ?>
      <p class="meta">
        No query has been run yet. Enter optional filters above and click <strong>Search</strong>,
        or click <strong>Display All Records</strong>.
      </p>
    <?php else: ?>
      <p class="meta"><?php echo count($rows); ?> record(s) found.</p>

      <?php if (count($rows) === 0): ?>
        <p class="meta">No matches found. Try a broader search or click <strong>Display All Records</strong>.</p>
      <?php else: ?>
        <div class="records">
          <?php foreach ($rows as $r): ?>
            <div class="record-card">
              <div class="record-header">
                <div>
                  <h3 class="record-title"><?php echo htmlspecialchars($r["style"]); ?></h3>
                  <div class="meta">
                    ID: <code><?php echo htmlspecialchars($r["id"]); ?></code>
                  </div>
                </div>
                <div class="pill"><?php echo htmlspecialchars($r["origin"]); ?></div>
              </div>

              <div class="record-grid">
                <div><span class="k">Age:</span> <?php echo htmlspecialchars((string)($r["age"] ?? "")); ?></div>
                <div><span class="k">Parent:</span> <?php echo htmlspecialchars((string)($r["parent"] ?? "")); ?></div>
                <div><span class="k">Founder:</span> <?php echo htmlspecialchars((string)($r["founder"] ?? "")); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
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