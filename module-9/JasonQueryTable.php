<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 8 sql connection and table management
 * February 25, 2026
 * 
 * JasonQueryTable.php
 * 
 * Purpose:
 *  - Connect to MySQL database and query the "martial_arts" table.
 *  - Display the contents of the table in an HTML table format.
 */
$conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$sql = "SELECT id, style, origin, age, parent, founder FROM martial_arts ORDER BY style";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "<h2>Martial Arts Table (martial_arts)</h2>";
echo "<table border='1' cellpadding='6' cellspacing='0'>";
echo "<tr>
        <th>ID</th>
        <th>Style</th>
        <th>Origin</th>
        <th>Age (years)</th>
        <th>Parent</th>
        <th>Founder</th>
      </tr>";

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["style"] . "</td>";
    echo "<td>" . $row["origin"] . "</td>";
    echo "<td>" . $row["age"] . "</td>";
    echo "<td>" . $row["parent"] . "</td>";
    echo "<td>" . $row["founder"] . "</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_free_result($result);
mysqli_close($conn);
?>