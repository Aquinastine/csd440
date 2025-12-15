<!-- Jason Luttrell
     CSD 440 Server-Side Scripting
     Module 2 Random Number Table
     December 14, 2025
-->

<!--Generates a randon number table and displays it in an html
document. The numbers are not cryptographically random -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"> <!-- Setting character encoding -->
    <title>PHP Random Number Table</title>
    <!-- Basic CSS styling for the page -->
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
        }
        td {
            border: 2px rgba(240, 114, 114, 0.62) solid;
            padding: 8px;
            text-align: center;
            color: white;            
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .card {
            background: #434e46ff;
            border-radius: 8px;
            padding: 20px 30px;
            box-shadow: 0 2px 6px rgba(148, 108, 108, 0.62);
            max-width: 300px;
        }
        h1 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #f7f4f4ff;
            text-align: center;
        }
    </style>
</head>
<body>


<div class="card">
    <h1>Random Number Table</h1>
    <table>
    <?php
    for ($row = 1; $row <= 5; $row++) {
    ?>
        <tr>
        <?php
        for ($col = 1; $col <= 5; $col++) {
            $randomNumber = rand(1, 100);
        ?>
            <td><?php echo $randomNumber; ?></td>
        <?php
        }
        ?>
        </tr>
    <?php
    }
    ?>
    </table>
</div>
</body>
</html>
