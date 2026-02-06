<?php
declare(strict_types=1);

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function isValidState(string $state, array $states): bool {
    return array_key_exists($state, $states);
}

$statesFile = __DIR__ . '/JasonData.json';

$statesJson = file_get_contents($statesFile);
if ($statesJson === false) {
    die("Unable to load JasonData.json");
}

$states = json_decode($statesJson, true);
if (!is_array($states)) {
    die("Invalid JSON in JasonData.json: " . json_last_error_msg());
}


$errors = [];
$data = [
    "name" => "",
    "addr1" => "",
    "addr2" => "",
    "city" => "",
    "state" => "",
    "zip" => "",
    "phone" => ""
];

$submitted = ($_SERVER["REQUEST_METHOD"] === "POST");
$resetRequested = $submitted && isset($_POST["reset"]);

if ($resetRequested) {
    // “Start Over” button: show the form again
    $submitted = false;
} elseif ($submitted) {
    // Collect + trim
    foreach ($data as $k => $_) {
        $data[$k] = trim((string)($_POST[$k] ?? ""));
    }

    // 1) Verify all fields populated
    foreach ($data as $k => $v) {
        if ($v === "") {
            $errors[] = "Field is required: " . strtoupper($k);
        }
    }

    // 2) Validate format (simple, not too technical)
    if ($data["name"] !== "" && !preg_match("/^[A-Za-z .'-]{2,60}$/", $data["name"])) {
        $errors[] = "Name must be 2–60 characters (letters/spaces/basic punctuation).";
    }

    if ($data["addr1"] !== "" && strlen($data["addr1"]) < 5) {
        $errors[] = "Address 1st line looks too short.";
    }

    if ($data["addr2"] !== "" && strlen($data["addr2"]) < 2) {
        $errors[] = "Address 2nd line looks too short.";
    }

    if ($data["city"] !== "" && !preg_match("/^[A-Za-z .'-]{2,60}$/", $data["city"])) {
        $errors[] = "City must be 2–60 characters (letters/spaces).";
    }

    if ($data["state"] !== "" && !isValidState($data["state"], $states)) {
        $errors[] = "Please select a valid U.S. state.";
    }

    // ZIP: 5 digits (simple version)
    if ($data["zip"] !== "" && !preg_match("/^\d{5}$/", $data["zip"])) {
        $errors[] = "ZIP must be exactly 5 digits (e.g., 12345).";
    }

    // Phone: allow digits plus basic formatting; then require 10 digits total
    if ($data["phone"] !== "") {
        $digits = preg_replace("/\D+/", "", $data["phone"]);
        if (strlen($digits) !== 10) {
            $errors[] = "Phone must have 10 digits (e.g., 5551234567).";
        } else {
            // normalize for display
            $data["phone"] = $digits;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>JasonForm</title>
    <link rel="stylesheet" href="JasonStyles.css" />
</head>
<body>

<div class="card">
    <h1>Jason Address Form</h1>
    <p class="meta">
        Enter all address fields below. On submit, the form card is replaced with a response card.
        If anything is missing or formatted incorrectly, an error card is shown.
    </p>
</div>

<?php if (!$submitted): ?>
    <!-- FORM CARD -->
    <div class="card">
        <h2>Address Card</h2>

        <form method="POST" action="">
            <div class="row">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="<?php echo h($data["name"]); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="addr1">Address 1st line</label>
                    <input id="addr1" name="addr1" type="text" value="<?php echo h($data["addr1"]); ?>" required>
                </div>
                <div class="field">
                    <label for="addr2">Address 2nd line (Apt/Suite)</label>
                    <input id="addr2" name="addr2" type="text" value="<?php echo h($data["addr2"]); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="city">City</label>
                    <input id="city" name="city" type="text" value="<?php echo h($data["city"]); ?>" required>
                </div>
                <div class="field">
                    <label for="state">State</label>
                    <select id="state" name="state" required>
                        <option value="">-- Select a State --</option>
                        <?php foreach ($states as $abbr => $label): ?>
                            <option value="<?php echo h($abbr); ?>"
                                <?php echo ($data["state"] === $abbr) ? "selected" : ""; ?>>
                                <?php echo h($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="zip">ZIP Code</label>
                    <input id="zip" name="zip" type="text" value="<?php echo h($data["zip"]); ?>" required placeholder="12345">
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input id="phone" name="phone" type="tel" value="<?php echo h($data["phone"]); ?>" required placeholder="555-123-4567">
                </div>
            </div>

            <div class="row">
                <button type="submit">Submit</button>
                <button class="secondary" type="submit" name="reset" value="1" formnovalidate>
                Refresh Form </button>
            </div>
        </form>
    </div>

<?php else: ?>

    <?php if (!empty($errors)): ?>
        <!-- ERROR CARD -->
        <div class="card">
            <h2>Validation Errors</h2>
            <div class="error">
                <?php foreach ($errors as $e): ?>
                    <div><?php echo h($e); ?></div>
                <?php endforeach; ?>
            </div>

            <form method="POST" action="" style="margin-top:14px;">
                <button class="secondary" type="submit" name="reset" value="1">Back to Form</button>
            </form>
        </div>

    <?php else: ?>
        <!-- RESPONSE CARD (REPLACES FORM CARD) -->
        <div class="card">
            <h2>Address Submitted Successfully</h2>
            <p class="meta">All fields were populated and the data format looks correct.</p>

            <table class="tbl">
                <thead>
                    <tr><th>Field</th><th>Value</th></tr>
                </thead>
                <tbody>
                    <tr><td>Name</td><td><?php echo h($data["name"]); ?></td></tr>
                    <tr><td>Address 1st line</td><td><?php echo h($data["addr1"]); ?></td></tr>
                    <tr><td>Address 2nd line</td><td><?php echo h($data["addr2"]); ?></td></tr>
                    <tr><td>City</td><td><?php echo h($data["city"]); ?></td></tr>
                    <tr><td>State</td><td><?php echo h($states[$data["state"]] ?? $data["state"]); ?></td></tr>
                    <tr><td>ZIP</td><td><?php echo h($data["zip"]); ?></td></tr>
                    <tr><td>Phone</td><td><?php echo h($data["phone"]); ?></td></tr>
                </tbody>
            </table>

            <form method="POST" action="" style="margin-top:14px;">
                <button class="secondary" type="submit" name="reset" value="1">Enter Another Address</button>
            </form>

            <p class="footer">
                Server time: <strong><?php echo date("Y-m-d H:i:s"); ?></strong>
            </p>
        </div>
    <?php endif; ?>

<?php endif; ?>
</body>
</html>