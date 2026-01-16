<!-- Jason Luttrell
     CSD 440 Server-Side Scripting
     Module 4 Palindrome Checker (HTML Card)
     January 15, 2026
-->
<?php
session_start();

/**
 * normalizeString
 * Converts input to lowercase and removes all non-alphanumeric characters.
 */
function normalizeString(string $text): string
{
    return preg_replace("/[^a-z0-9]/", "", strtolower($text));
}

/**
 * isPalindrome
 * Checks if a string is a palindrome after normalization.
 */
function isPalindrome(string $text): bool
{
    $normalized = normalizeString($text);
    return $normalized === strrev($normalized);
}

/* --------------------------- Pre-Checked History --------------------------- */
$checkedHistory = [
    "racecar",
    "Never odd or even",
    "A man, a plan, a canal: Panama!",
    "hello",
    "OpenAI",
    "server side scripting"
];

/* --------------------------- Session History Setup --------------------------- */
if (!isset($_SESSION["userHistory"])) {
    $_SESSION["userHistory"] = [];
}

/* --------------------------- Form Handling Logic --------------------------- */
$input = $_POST["text"] ?? "";
$resultMessage = "";
$resultClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $trimmed = trim($input);

    if ($trimmed === "") {
        $resultMessage = "Please enter a string to test.";
        $resultClass = "warn";
    } else {
        $isPal = isPalindrome($trimmed);
        $reversed = strrev($trimmed);

        // Save user submission to session history
        $_SESSION["userHistory"][] = $trimmed;

        $resultMessage =
            "Original: <strong>" . htmlspecialchars($trimmed) . "</strong><br>" .
            "Reversed: <strong>" . htmlspecialchars($reversed) . "</strong><br><br>" .
            "Result: <strong>" . ($isPal ? "PALINDROME ✅" : "NOT a palindrome ❌") . "</strong>";

        $resultClass = $isPal ? "ok" : "bad";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Palindrome Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            max-width: 560px;
            margin-bottom: 30px;
        }
        h1, h2, h3 {
            margin-top: 0;
            color: #333;
        }
        .meta {
            font-size: 0.9rem;
            color: #555;
        }
        label {
            display: block;
            margin: 14px 0 6px 0;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        button {
            margin-top: 12px;
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            background: #333;
            color: #fff;
            cursor: pointer;
        }
        .result {
            margin-top: 16px;
            padding: 12px;
            border-radius: 6px;
            background: #fafafa;
            border: 1px solid #ddd;
        }
        .ok { color: green; font-weight: bold; }
        .bad { color: #b00020; font-weight: bold; }
        .warn { color: #b36b00; font-weight: bold; }
        ol {
            padding-left: 20px;
        }
        li {
            margin-bottom: 8px;
        }
        .divider {
            margin: 20px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>

<!-- --------------------------- Palindrome Checker Card --------------------------- -->
<div class="card">
    <h1>Palindrome Checker</h1>
    <p class="meta">
        Enter a string and I’ll tell you if it reads the same forward and backward
        (ignoring spaces, punctuation, and case).
    </p>

    <form method="POST" action="">
        <label for="text">String to test:</label>
        <input
            type="text"
            id="text"
            name="text"
            value="<?php echo htmlspecialchars($input); ?>"
            placeholder="Example: Never odd or even"
        >
        <button type="submit">Check</button>
    </form>

    <?php if ($resultMessage !== ""): ?>
        <div class="result <?php echo $resultClass; ?>">
            <?php echo $resultMessage; ?>
        </div>
    <?php endif; ?>
</div>

<!-- --------------------------- History Card --------------------------- -->
<div class="card">
    <h2>Checked Words (History)</h2>

    <h3>Preloaded Examples</h3>
    <ol>
        <?php foreach ($checkedHistory as $word): ?>
            <li>
                <strong><?php echo htmlspecialchars($word); ?></strong> —
                <?php echo isPalindrome($word)
                    ? "<span class='ok'>Palindrome</span>"
                    : "<span class='bad'>Not a palindrome</span>"; ?>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="divider"></div>

    <h3>User Checked Strings</h3>

    <?php if (empty($_SESSION["userHistory"])): ?>
        <p class="meta">No user strings checked yet.</p>
    <?php else: ?>
        <ol>
            <?php foreach ($_SESSION["userHistory"] as $userWord): ?>
                <li>
                    <strong><?php echo htmlspecialchars($userWord); ?></strong> —
                    <?php echo isPalindrome($userWord)
                        ? "<span class='ok'>Palindrome</span>"
                        : "<span class='bad'>Not a palindrome</span>"; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>

</body>
</html>