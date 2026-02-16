<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 8 sql connection and table management
 * February 25, 2026
 * 
 * JasonDropTable.php
 * 
 * Purpose:
 *  - Connect to MySQL database and drop the table named "martial_arts".
 */
$conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$sql = "DROP TABLE IF EXISTS martial_arts";

if (mysqli_query($conn, $sql)) {
    echo "Table 'martial_arts' dropped successfully.";
} else {
    echo "Error dropping table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>