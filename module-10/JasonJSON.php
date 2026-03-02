<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 10
 * March 1 2026
 * 
 * File: JasonJSON.php
 * Purpose: Display a form (8+ fields), validate submission, json_encode the data,
 *          and display either a formatted JSON output or an error display.
 */

declare(strict_types=1);

/**
 * Safely fetch and trim a POST value.
 *
 * @param string $key
 * @return string
 */
function post_value(string $key): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

/**
 * Escape output for safe HTML rendering.
 *
 * @param string $value
 * @return string
 */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sticky field helper.
 *
 * @param string $name
 * @return string
 */
function sticky(string $name): string
{
    return h(post_value($name));
}

$errors = [];
$jsonOut = '';

$isPost = (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');

if ($isPost) {
    // --- Collect (9 fields; assignment requires minimum 8) ---
    $firstName = post_value('firstName');
    $lastName  = post_value('lastName');
    $email     = post_value('email');
    $phone     = post_value('phone');
    $age       = post_value('age');
    $city      = post_value('city');
    $state     = post_value('state');
    $studentId = post_value('studentId');
    $notes     = post_value('notes'); // optional but included as a field

    // --- Required checks ---
    if ($firstName === '') $errors[] = "First Name is required.";
    if ($lastName === '')  $errors[] = "Last Name is required.";
    if ($email === '')     $errors[] = "Email is required.";
    if ($phone === '')     $errors[] = "Phone is required.";
    if ($age === '')       $errors[] = "Age is required.";
    if ($city === '')      $errors[] = "City is required.";
    if ($state === '')     $errors[] = "State is required.";
    if ($studentId === '') $errors[] = "Student ID is required.";

    // --- Format validation ---
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email format is invalid.";
    }

    if ($age !== '') {
        if (!ctype_digit($age)) {
            $errors[] = "Age must be a whole number.";
        } else {
            $ageInt = (int)$age;
            if ($ageInt < 1 || $ageInt > 120) {
                $errors[] = "Age must be between 1 and 120.";
            }
        }
    }

    // Basic phone characters: digits + common symbols
    if ($phone !== '' && !preg_match('/^[0-9\-\(\)\s\+\.]{7,25}$/', $phone)) {
        $errors[] = "Phone contains invalid characters.";
    }

    // State as 2 letters (e.g., VA)
    if ($state !== '' && !preg_match('/^[A-Za-z]{2}$/', $state)) {
        $errors[] = "State must be a 2-letter code (e.g., VA).";
    }

    // Student ID: letters/numbers/-/_
    if ($studentId !== '' && !preg_match('/^[A-Za-z0-9\-_]{3,30}$/', $studentId)) {
        $errors[] = "Student ID must be 3–30 characters (letters, numbers, - or _).";
    }

    // --- Encode JSON if no errors ---
    if (count($errors) === 0) {
        $data = [
            "firstName" => $firstName,
            "lastName"  => $lastName,
            "email"     => $email,
            "phone"     => $phone,
            "age"       => (int)$age,
            "city"      => $city,
            "state"     => strtoupper($state),
            "studentId" => $studentId,
            "notes"     => $notes
        ];

        $jsonOut = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($jsonOut === false) {
            $errors[] = "json_encode failed: " . json_last_error_msg();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Jason JSON Form</title>

  <!-- Use the attached CSS as the template -->
  <link rel="stylesheet" href="JasonStyles.css" />
</head>
<body>

  <!-- Page container card -->
  <div class="card">
    <h1>JSON Form Submission</h1>
    <p class="meta">
      Enter at least 8 fields and submit. The server validates your input, then returns the JSON produced by
      <code>json_encode()</code>.
    </p>

    <div class="section">
      <h2>Enter Your Data</h2>

      <form method="POST" action="">
        <div class="row">
          <div class="field">
            <label for="firstName">First Name *</label>
            <input id="firstName" name="firstName" type="text" value="<?= sticky('firstName') ?>" required />
          </div>

          <div class="field">
            <label for="lastName">Last Name *</label>
            <input id="lastName" name="lastName" type="text" value="<?= sticky('lastName') ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="field">
            <label for="email">Email *</label>
            <input id="email" name="email" type="text" value="<?= sticky('email') ?>" required />
          </div>

          <div class="field">
            <label for="phone">Phone *</label>
            <input id="phone" name="phone" type="tel" value="<?= sticky('phone') ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="field">
            <label for="age">Age *</label>
            <input id="age" name="age" type="number" min="1" max="120" value="<?= sticky('age') ?>" required />
          </div>

          <div class="field">
            <label for="studentId">Student ID *</label>
            <input id="studentId" name="studentId" type="text" value="<?= sticky('studentId') ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="field">
            <label for="city">City *</label>
            <input id="city" name="city" type="text" value="<?= sticky('city') ?>" required />
          </div>

          <div class="field">
            <label for="state">State (2 letters) *</label>
            <input id="state" name="state" type="text" maxlength="2" value="<?= sticky('state') ?>" required />
          </div>
        </div>

        <div class="row">
          <div class="field" style="flex: 1 1 100%;">
            <label for="notes">Notes (optional)</label>
            <input id="notes" name="notes" type="text" value="<?= sticky('notes') ?>" />
          </div>
        </div>

        <div class="actions">
          <button type="submit">Submit</button>
          <button type="reset" class="secondary">Reset</button>
        </div>
      </form>

      <?php if ($isPost): ?>
        <?php if (count($errors) > 0): ?>
          <div class="error">
            Error Display — fix the following and submit again:
            <ul>
              <?php foreach ($errors as $e): ?>
                <li><?= h($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php else: ?>
          <div class="ok">
            Success — your form data was encoded into JSON.
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Output card (still a card, per your requirement) -->
  <?php if ($isPost && count($errors) === 0): ?>
    <div class="card">
      <h2>JSON Output Display</h2>
      <p class="meta">Below is the JSON returned by <code>json_encode()</code> (pretty printed):</p>

      <div class="records">
        <div class="record-card">
          <div class="record-header">
            <h3 class="record-title">Encoded JSON</h3>
            <span class="pill">OK</span>
          </div>

          <div class="section">
            <pre><code><?= h($jsonOut) ?></code></pre>
          </div>

          <div class="footer">
            Tip: If you submit invalid input, this JSON card won’t render—only the error card will.
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</body>
</html>