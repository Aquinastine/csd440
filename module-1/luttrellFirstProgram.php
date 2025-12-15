<!-- Jason Luttrell
     CSD 440 Server-Side Scripting
     Module 1 First PHP Program
     June 5, 2024
-->

<!--The php comment below is the first test of the script. if 
the comment doesnt show up then php is working and the syntax is
correct. If there is an error in the syntax then the page will 
not render. -->
<?php
//luttrellFirstProgram.php - simple PHP test page that provides server
//time. and PHP version There is no valid rendering if php is not 
//working. so the result will simply be no date, no version. with 
//everything else intact.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Setting character encoding -->
    <title>PHP Test Page</title>
    <!-- Basic CSS styling for the page -->
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
            max-width: 500px;
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .meta {
            font-size: 0.9rem;
            color: #555;
        }
        .ok {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>PHP is Working ✅</h1>
    <p class="meta">
        Server time: 
        <strong>
            <?php echo date('Y-m-d H:i:s'); ?>
        </strong>
    </p>
    <p class="meta">
        PHP version: 
        <strong>
            //
            <?php echo phpversion(); ?> 
        </strong>
    </p>
    <p>
        If you can see this page with the time and PHP version filled in,
        your PHP environment is <span class="ok">configured correctly</span>.
    </p>
</div>
</body>
</html>
