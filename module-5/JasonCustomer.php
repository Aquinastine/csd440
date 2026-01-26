<?php
declare(strict_types=1);
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 5 Customers Array Program
 * January 25, 2026
 * 
 * JasonCustomers.php
 *
 * Purpose:
 *  - Create and display an array of customers (10+).
 *  - Each customer includes: first name, last name, age, phone number.
 *  - Use array methods to find records based on different fields.
 *  - Display output in a card layout (same style as prior PHP program).
 *
 * Notes:
 *  - This program demonstrates searching by last name, by age threshold,
 *    and by phone area code, using built-in PHP array functions.
 */



/**
 * Asserts a condition is true. If not, stops execution with a clear message.
 *
 * @param bool $condition
 * @param string $message
 * @return void
 */
function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        echo "<p style='color:#b00020;font-weight:bold;'>TEST FAILED:</p>";
        echo "<p>" . htmlspecialchars($message) . "</p>";
        exit(1);
    }
}

/**
 * Formats a US phone number string into a consistent display.
 * Expects digits-only like "2035550101" or returns original if not 10 digits.
 *
 * @param string $digits
 * @return string
 */
function formatPhone(string $digits): string
{
    $clean = preg_replace('/\D+/', '', $digits);
    if (strlen($clean) !== 10) {
        return $digits;
    }
    return sprintf("(%s) %s-%s",
        substr($clean, 0, 3),
        substr($clean, 3, 3),
        substr($clean, 6, 4)
    );
}

/**
 * Creates a normalized phone string (digits only).
 *
 * @param string $phone
 * @return string
 */
function phoneDigits(string $phone): string
{
    return preg_replace('/\D+/', '', $phone);
}

/**
 * Returns customers whose last name matches (case-insensitive).
 *
 * @param array $customers
 * @param string $lastName
 * @return array
 */
function findByLastName(array $customers, string $lastName): array
{
    $target = strtolower(trim($lastName));

    return array_values(array_filter($customers, function ($c) use ($target) {
        return strtolower($c['lastName']) === $target;
    }));
}

/**
 * Returns customers with age >= $minAge.
 *
 * @param array $customers
 * @param int $minAge
 * @return array
 */
function findByMinAge(array $customers, int $minAge): array
{
    return array_values(array_filter($customers, function ($c) use ($minAge) {
        return (int)$c['age'] >= $minAge;
    }));
}

/**
 * Returns customers whose phone number starts with a given area code.
 *
 * @param array $customers
 * @param string $areaCode 3-digit area code (e.g., "203")
 * @return array
 */
function findByAreaCode(array $customers, string $areaCode): array
{
    $areaCode = preg_replace('/\D+/', '', $areaCode);

    return array_values(array_filter($customers, function ($c) use ($areaCode) {
        $digits = phoneDigits($c['phone']);
        return strlen($digits) >= 3 && substr($digits, 0, 3) === $areaCode;
    }));
}

/**
 * Renders a list of customers as an HTML table.
 *
 * @param array $customers
 * @return string
 */
function renderCustomerTable(array $customers): string
{
    if (count($customers) === 0) {
        return "<p class='meta'>No matching customers found.</p>";
    }

    $html = "<table class='tbl'>
                <thead>
                    <tr>
                        <th>First</th>
                        <th>Last</th>
                        <th>Age</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($customers as $c) {
        $html .= "<tr>"
            . "<td>" . htmlspecialchars($c['firstName']) . "</td>"
            . "<td>" . htmlspecialchars($c['lastName']) . "</td>"
            . "<td>" . htmlspecialchars((string)$c['age']) . "</td>"
            . "<td>" . htmlspecialchars(formatPhone($c['phone'])) . "</td>"
            . "</tr>";
    }

    $html .= "</tbody></table>";
    return $html;
}

/* --------------------------- DATA SETUP (10+ customers) --------------------------- */

$customers = [
    ["firstName" => "Hugo",   "lastName" => "Ramirez",   "age" => 28, "phone" => "2035550101"],
    ["firstName" => "Alex",   "lastName" => "Johnson",  "age" => 41, "phone" => "8605550110"],
    ["firstName" => "Taylor", "lastName" => "Smith",    "age" => 35, "phone" => "2035550199"],
    ["firstName" => "Jordan", "lastName" => "Baker",    "age" => 22, "phone" => "4755550133"],
    ["firstName" => "Casey",  "lastName" => "Nguyen",   "age" => 30, "phone" => "2125550144"],
    ["firstName" => "Morgan", "lastName" => "Ramirez",  "age" => 52, "phone" => "2035550155"],
    ["firstName" => "Avery",  "lastName" => "Lee",      "age" => 19, "phone" => "6465550166"],
    ["firstName" => "Riley",  "lastName" => "Garcia",   "age" => 44, "phone" => "8605550177"],
    ["firstName" => "Parker", "lastName" => "Davis",    "age" => 27, "phone" => "2035550188"],
    ["firstName" => "Quinn",  "lastName" => "Wilson",   "age" => 33, "phone" => "9145550123"],
];

/* --------------------------- THOROUGH TESTS --------------------------- */

// Basic dataset validation
assertTrue(count($customers) >= 10, "Customer array must include at least 10 customers.");

// Ensure each customer record has required keys
$requiredKeys = ["firstName", "lastName", "age", "phone"];
foreach ($customers as $idx => $c) {
    foreach ($requiredKeys as $k) {
        assertTrue(array_key_exists($k, $c), "Customer index $idx is missing required field: $k");
    }
    assertTrue(is_numeric($c["age"]), "Customer index $idx age must be numeric.");
    assertTrue(strlen(phoneDigits($c["phone"])) >= 10, "Customer index $idx phone should have at least 10 digits.");
}

// Search tests
$ramirez = findByLastName($customers, "Ramirez");
assertTrue(count($ramirez) === 2, "Expected 2 customers with last name Ramirez.");

$age40plus = findByMinAge($customers, 40);
assertTrue(count($age40plus) >= 2, "Expected at least 2 customers aged 40+.");

$area203 = findByAreaCode($customers, "203");
assertTrue(count($area203) >= 4, "Expected at least 4 customers with 203 area code.");

/* --------------------------- SEARCH DEMOS (array methods) --------------------------- */

$foundLastName = findByLastName($customers, "Ramirez");
$foundAge = findByMinAge($customers, 40);
$foundArea = findByAreaCode($customers, "203");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers Array Program</title>
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
            max-width: 850px;
            margin-bottom: 30px;
        }
        h1, h2 {
            margin-top: 0;
            color: #333;
        }
        .meta {
            font-size: 0.95rem;
            color: #555;
        }
        .ok {
            color: green;
            font-weight: bold;
        }
        .tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .tbl th, .tbl td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid #e4e4e4;
        }
        .tbl th {
            background: #fafafa;
        }
        .section {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
        }
        .footer {
            margin-top: 14px;
            font-size: 0.85rem;
            color: #666;
        }
        code {
            background: #f2f2f2;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Customer Records</h1>
    <p class="meta">
        This page creates an array of customers (first name, last name, age, phone) and
        demonstrates searching the array using PHP array methods.
        Tests are executed at load time—if the page renders, all tests passed.
    </p>

    <div class="section">
        <h2>All Customers (<?php echo count($customers); ?>)</h2>
        <?php echo renderCustomerTable($customers); ?>
    </div>

    <div class="section">
        <h2>Search 1: Find by Last Name (<code>Ramirez</code>)</h2>
        <p class="meta">Uses <code>array_filter()</code> and a case-insensitive match on last name.</p>
        <?php echo renderCustomerTable($foundLastName); ?>
    </div>

    <div class="section">
        <h2>Search 2: Find Customers Age 40+ (<code>&gt;= 40</code>)</h2>
        <p class="meta">Uses <code>array_filter()</code> to include customers meeting an age threshold.</p>
        <?php echo renderCustomerTable($foundAge); ?>
    </div>

    <div class="section">
        <h2>Search 3: Find by Area Code (<code>203</code>)</h2>
        <p class="meta">Uses <code>array_filter()</code> after normalizing the phone number to digits.</p>
        <?php echo renderCustomerTable($foundArea); ?>
    </div>

    <p class="footer">
        Status: <span class="ok">All tests passed ✅</span><br>
        Server time: <strong><?php echo date('Y-m-d H:i:s'); ?></strong> |
        PHP version: <strong><?php echo phpversion(); ?></strong>
    </p>
</div>

</body>
</html>