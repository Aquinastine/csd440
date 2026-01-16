<!-- Jason Luttrell
     CSD 440 Server-Side Scripting
     Module 4 Palindrome Checker (HTML Card)
     January 15, 2026
-->
<?php
/**
 * JasonPalindrome.php
 * Purpose: Render an HTML page with a simple “card” UI that checks whether
 *          a user-entered string is a palindrome.
 *
 * Notes:
 *  - The palindrome check ignores spaces, punctuation, and letter casing.
 *  - Example: "Never odd or even" is treated as a palindrome.
 */

/**
 * normalizeString
 * Converts input to lowercase and removes all non-alphanumeric characters.
 *
 * @param string $text
 * @return string
 */
function normalizeString(string $text): string
{
    $lower = strtolower($text);
    return preg_replace("/[^a-z0-9]/", "", $lower);
}

/**
 * isPalindrome
 * Checks if a string is a palindrome after normalization.
 *
 * @param string $text
 * @return bool
 */
function isPalindrome(string $text): bool
{
    $normalized = normalizeString($text);
    return $normalized === strrev($normalized);
}

/**
 * A small test helper to verify the palindrome function is working correctly.
 * If a test fails, the script stops and prints an error message.
 *
 * @param string $text
 * @param bool $expected
 * @return void
 */
function assertPalindrome(string $text, bool $expected): void
{
    $actual = isPalindrome($text);
    if ($actual !== $expected) {
        echo "TEST FAILED for: \"$text\". Expected: " . ($expected ? "true" : "false")
            . " Actual: " . ($actual ? "true" : "false");
        exit(1);
    }
}

/* --------------------------- Basic Function Tests --------------------------- */
assertPalindrome("racecar", true);
assertPalindrome("Never odd or even", true);
assertPalindrome("A man, a plan, a canal: Panama!", true);

assertPalindrome("hello", false);
assertPalindrome("OpenAI is awesome", false);
assertPalindrome("palindrome test", false);

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

        // Show string in both orders (original and reversed)
        $reversed = strrev($trimmed);

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
        }
        h1 {
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
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            box-sizing: border-box;
        }
        button {
            margin-top: 12px;
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: #333;
            color: #fff;
            font-size: 1rem;
        }
        button:hover {
            opacity: 0.9;
        }
        .result {
            margin-top: 16px;
            padding: 12px;
            border-radius: 6px;
            background: #fafafa;
            border: 1px solid #ddd;
            line-height: 1.45;
        }
        .ok { color: green; font-weight: bold; }
        .bad { color: #b00020; font-weight: bold; }
        .warn { color: #b36b00; font-weight: bold; }
        .hint {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Palindrome Checker</h1>
    <p class="meta">
        Enter a string and I’ll tell you if it reads the same forward and backward
        (ignoring spaces/punctuation/case).
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

    <p class="hint">
        Quick examples: <em>racecar</em> (palindrome), <em>hello</em> (not palindrome)
    </p>
</div>
</body>
</html>