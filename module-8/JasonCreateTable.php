<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 8 sql connection and table management
 * February 25, 2026
 * 
 * JasonCreateTable.php
 * 
 * Purpose:
 *  - Connect to MySQL database and create a new table named "martial_arts".
 *  - The table includes fields for id, style, origin, age, parent, and founder.
 */
$conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE TABLE martial_arts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    style VARCHAR(100) NOT NULL,
    origin VARCHAR(100) NOT NULL,
    age INT,
    parent VARCHAR(100),
    founder VARCHAR(100)
)";

if (mysqli_query($conn, $sql)) {
    echo "Table 'martial_arts' created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
