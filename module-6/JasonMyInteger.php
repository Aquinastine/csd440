<?php
declare(strict_types=1);
session_start();

/**
 * Simple test helper: stops the page if a test fails.
 */
function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        echo "<div style='padding:16px;background:#fff;border-radius:8px;max-width:850px;
                     box-shadow:0 2px 6px rgba(0,0,0,0.15);margin:30px auto;font-family:Arial,sans-serif;'>
                <p style='color:#b00020;font-weight:bold;margin:0 0 10px 0;'>TEST FAILED:</p>
                <p style='margin:0;'>" . htmlspecialchars($message) . "</p>
              </div>";
        exit(1);
    }
}

/**
 * JasonMyInteger
 * Holds one integer and provides helper methods.
 */
class JasonMyInteger
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /** Getter */
    public function getValue(): int
    {
        return $this->value;
    }

    /** Setter */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /** Returns true if the provided number is even */
    public function isEven(int $n): bool
    {
        return ($n % 2) === 0;
    }

    /** Returns true if the provided number is odd */
    public function isOdd(int $n): bool
    {
        return ($n % 2) !== 0;
    }

    /**
     * Returns true if THIS object's stored integer is prime.
     */
    public function isPrime(): bool
    {
        $n = $this->value;

        if ($n <= 1) return false;
        if ($n === 2) return true;
        if ($n % 2 === 0) return false;

        for ($i = 3; $i * $i <= $n; $i += 2) {
            if ($n % $i === 0) return false;
        }
        return true;
    }
}

/* --------------------------- FORM HANDLING --------------------------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle refresh request
    if (isset($_POST['refresh'])) {
        unset($_SESSION['my_integer_value']);
        unset($_SESSION['show_new_test_card']);
        unset($_SESSION['last_set_new_value']);
        unset($_SESSION['my_integer_error']);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Existing set / set_new logic
    $raw = $_POST['myInt'] ?? '';
    $action = $_POST['action'] ?? 'set';


    // Basic integer validation (supports negative)
    if (preg_match('/^-?\d+$/', trim((string)$raw))) {
        $_SESSION['my_integer_value'] = (int)$raw;

        // Only show the "new class test" card when Set New is pressed
        if ($action === 'set_new') {
            $_SESSION['show_new_test_card'] = true;
            $_SESSION['last_set_new_value'] = (int)$raw; // store what "Set New" used
        }
    } else {
        $_SESSION['my_integer_error'] = "Please enter a valid integer.";
    }

    // Prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Stored value used by the form (still stored even if built-in card doesn't use it)
$hasValue = array_key_exists('my_integer_value', $_SESSION);
$currentValue = $hasValue ? (int)$_SESSION['my_integer_value'] : 10;

// Button label logic
$btnText = "Set New"; //$hasValue ? "Set New" : "Set";
$actionValue = "set_new"; //$hasValue ? "set_new" : "set";

// Error message
$errorMsg = $_SESSION['my_integer_error'] ?? '';
unset($_SESSION['my_integer_error']);

// Whether to show the new test card (only after Set New press)
$showNewTestCard = !empty($_SESSION['show_new_test_card']);
$newTestValue = $_SESSION['last_set_new_value'] ?? null;

/* --------------------------- BUILT-IN TESTS (UNCHANGED) ---------------------------
   IMPORTANT: This card is intentionally NOT tied to the form value.
   It always tests fixed values so it stays "un updated."
--------------------------------------------------------------------------- */

// Built-in fixed instances
$builtInA = new JasonMyInteger(10);
$builtInB = new JasonMyInteger(17);

// Built-in assertions
assertTrue($builtInA->getValue() === 10, "Built-in A value should be 10.");
assertTrue($builtInB->getValue() === 17, "Built-in B value should be 17.");

$setterTest = new JasonMyInteger(1);
$setterTest->setValue(99);
assertTrue($setterTest->getValue() === 99, "Setter should update stored integer.");

assertTrue($builtInA->isEven(10) === true, "isEven(10) should be true.");
assertTrue($builtInA->isOdd(10) === false, "isOdd(10) should be false.");
assertTrue((new JasonMyInteger(29))->isPrime() === true, "29 should be prime.");
assertTrue((new JasonMyInteger(21))->isPrime() === false, "21 should not be prime.");
assertTrue((new JasonMyInteger(1))->isPrime() === false, "1 should not be prime.");

/* --------------------------- NEW CLASS TEST CARD (ONLY AFTER SET NEW) --------------------------- */

$newInstance = null;
if ($showNewTestCard && $newTestValue !== null) {
    $newInstance = new JasonMyInteger((int)$newTestValue);

    // Quick sanity checks for the newly created instance
    assertTrue($newInstance->getValue() === (int)$newTestValue, "New instance value should match Set New value.");
}

/* --------------------------- DISPLAY HELPERS --------------------------- */

function yesNo(bool $b): string
{
    return $b ? "Yes" : "No";
}

function renderResultsTable(JasonMyInteger $obj, string $label): string
{
    $v = $obj->getValue();
    $rows = [
        ["Stored Integer", (string)$v],
        ["isEven(value)", yesNo($obj->isEven($v))],
        ["isOdd(value)", yesNo($obj->isOdd($v))],
        ["isPrime()", yesNo($obj->isPrime())],
    ];

    $html = "<h2 style='margin-bottom:8px;'>" . htmlspecialchars($label) . "</h2>";
    $html .= "<table class='tbl'><thead><tr><th>Test</th><th>Result</th></tr></thead><tbody>";

    foreach ($rows as $r) {
        $html .= "<tr><td>" . htmlspecialchars($r[0]) . "</td><td>" . htmlspecialchars($r[1]) . "</td></tr>";
    }

    $html .= "</tbody></table>";
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Integer Class Tester</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px; }
        .card {
            background: #ffffff; border-radius: 8px; padding: 20px 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15); max-width: 850px; margin-bottom: 30px;
        }
        h1, h2 { margin-top: 0; color: #333; }
        .meta { font-size: 0.95rem; color: #555; }
        .tbl { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .tbl th, .tbl td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e4e4e4; }
        .tbl th { background: #fafafa; }
        .section { margin-top: 18px; padding-top: 12px; border-top: 1px solid #ddd; }
        .footer { margin-top: 14px; font-size: 0.85rem; color: #666; }
        code { background: #f2f2f2; padding: 2px 6px; border-radius: 4px; }
        .row { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; margin-top: 10px; }
        .field { flex: 1; min-width: 220px; }
        label { display: block; font-size: 0.9rem; color: #333; margin-bottom: 6px; }
        input[type="number"] { width: 100%; padding: 10px; border: 1px solid #d0d0d0; border-radius: 6px; font-size: 1rem; }
        button { padding: 10px 16px; border: none; border-radius: 6px; background: #333; color: #fff; cursor: pointer; font-size: 1rem; }
        button:hover { opacity: 0.92; }
        .error { margin-top: 10px; color: #b00020; font-weight: bold; }
        .ok { color: green; font-weight: bold; }
    </style>
</head>
<body>

<!-- Card 1: Form / Setter -->
<div class="card">
    <h1>New Integer Class Tester</h1>
    <p class="meta">
        Enter an integer and click <code><?php echo htmlspecialchars($btnText); ?></code>.
        The stored value updates in the background.
    </p>

    <form method="POST" action="">
    <input type="hidden" name="action" value="<?php echo htmlspecialchars($actionValue); ?>">

    <div class="row">
        <div class="field">
            <label for="myInt">Integer Value</label>
            <input id="myInt" name="myInt" type="number" step="1"
                   value="<?php echo htmlspecialchars((string)$currentValue); ?>" required>
        </div>

        <div style="display:flex; gap:10px;">

            <!-- Set New Button -->
            <button type="submit"><?php echo htmlspecialchars($btnText); ?></button>

            <!-- Refresh Form Button -->
            <button type="submit" name="refresh" value="1">
                Refresh Form
            </button>
        </div>
    </div>

    <?php if ($errorMsg !== ''): ?>
        <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>
    </form>

    <p class="footer">
        Stored value (from form): <strong><?php echo htmlspecialchars((string)$currentValue); ?></strong><br>
        
    </p>
</div>
</div>

<!-- Card 2: New Class Test (HIDDEN until Set New pressed) -->
<?php if ($showNewTestCard && $newInstance !== null): ?>
<div class="card">
    <h1>New Class Test (After “Set New”)</h1>
    <p class="meta">
        This card only appears after pressing <code>Set New</code>.
        It tests the newly created instance using the last “Set New” value.
    </p>

    <div class="section">
        <?php echo renderResultsTable($newInstance, "New Instance (Value: " . $newInstance->getValue() . ")"); ?>
    </div>

    <p class="footer">
        Status: <span class="ok">New instance tests passed ✅</span>
    </p>
</div>

<?php endif; ?>

<!-- Card 3: Built In Test (UNCHANGED; does NOT use form value) -->
<div class="card">
    <h1>Built In Test</h1>
    <p class="meta">
        This card stays the same and always tests fixed values (10 and 17).
        If the page loads, these built-in tests passed.
    </p>

    <div class="section">
        <?php echo renderResultsTable($builtInA, "Built-in Instance A (Fixed Value: 10)"); ?>
    </div>

    <div class="section">
        <?php echo renderResultsTable($builtInB, "Built-in Instance B (Fixed Value: 17)"); ?>
    </div>

    <p class="footer">
        Status: <span class="ok">Built-in tests passed ✅</span>
    </p>
</div>



</body>
</html>
