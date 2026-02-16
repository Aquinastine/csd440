<?php
/**
 * Jason Luttrell
 * CSD 440 Server-Side Scripting
 * Module 8 sql connection and table management
 * February 25, 2026
 * 
 * JasonPopulateTable.php
 * 
 * Purpose:
 *  - Connect to MySQL database and populate the "martial_arts" table with sample data.
 */
$conn = mysqli_connect("localhost", "student1", "pass", "baseball_01");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// Note: ages are approximate (years old), and "parent" can be a related predecessor style.
$sql = "INSERT INTO martial_arts (style, origin, age, parent, founder) VALUES
('Judo', 'Japan', 143, 'Jujutsu', 'Jigoro Kano'),
('Aikido', 'Japan', 102, 'Daito-ryu Aiki-jujutsu', 'Morihei Ueshiba'),
('Taekwondo', 'Korea', 70, 'Karate / Korean martial traditions', 'Choi Hong Hi'),
('Brazilian Jiu-Jitsu', 'Brazil', 101, 'Judo', 'Carlos Gracie'),
('Krav Maga', 'Israel', 85, 'Boxing / Wrestling', 'Imi Lichtenfeld'),
('Muay Thai', 'Thailand', 700, 'Muay Boran', 'Unknown')";

if (mysqli_query($conn, $sql)) {
    echo "Records inserted successfully.";
} else {
    echo "Error inserting records: " . mysqli_error($conn);
}

mysqli_close($conn);
?>