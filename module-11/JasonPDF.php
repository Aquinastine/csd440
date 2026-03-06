<?php
/**
 * JasonPDF.php
 * CSD 440 - Module 11
 * date: 2024-06-10
 * Creates a PDF report that includes:
 *  1) General topic info (baseball database overview)
 *  2) ALL data from EVERY table in the baseball_01 database (each table rendered as a PDF table)
 *  3) PDF header + footer (title + generated date in header, page numbers in footer)
 */

// ---------- DB CONFIG ----------
$dbHost = "localhost";
$dbName = "baseball_01";
$dbUser = "student1";
$dbPass = "pass";

// ---------- FPDF ----------
require_once __DIR__ . "/fpdf186/fpdf.php";

class JasonPDF extends FPDF
{
    public string $reportTitle = "Jason Luttrell - Baseball_01 Database Report";
    public string $reportCaption = "CSD 440 - Module 11 - PDF Report";
    public string $generatedAt = "";
    public string $currentTable = "";

    /**
     * helper function to expose usable page width.
     */
    public function getUsablePageWidth(): float
    {
        return $this->GetPageWidth() - 20; // 10mm left + 10mm right
    }

    public function getUsablePageHeight(): float
    {
        return $this->GetPageHeight() - 20; // simple safe estimate
    }


    function Header()
    {
        // Header background line
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, $this->reportTitle, 0, 1, 'L');

        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 7, $this->reportCaption, 0, 1, 'L');

        $this->SetFont('Arial', '', 9);
        $subtitle = "Generated: " . $this->generatedAt;
        if ($this->currentTable !== "") {
            $subtitle .= "   |   Table: " . $this->currentTable;
        }
        $this->Cell(0, 5, $subtitle, 0, 1, 'L');

        // Divider
        $this->Ln(1);
        $this->Line($this->GetX(), $this->GetY(), $this->GetPageWidth() - $this->lMargin, $this->GetY());
        $this->Ln(4);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->Cell(0, 10, "Page " . $this->PageNo() . " of {nb}", 0, 0, 'C');
    }
}


/**
 * Safely converts any value to printable text for PDF cells.
 */
function cellText($val, int $maxLen = 40): string
{
    if ($val === null) return "NULL";
    if (is_bool($val)) return $val ? "true" : "false";
    $s = (string)$val;

    // Truncate to keep tables readable
    if (mb_strlen($s) > $maxLen) {
        $s = mb_substr($s, 0, $maxLen - 3) . "...";
    }

    return $s;
}

/**
 * Renders a table with a header row and all rows.
 * Automatically chooses column widths based on page width.
 */
function renderPdfTable(JasonPDF $pdf, array $columns, array $rows): void
{
    $numCols = max(1, count($columns));
    $usableWidth = 259;

    // Pick a font size and store it in a normal variable
    if ($numCols > 12) {
        $fontSize = 6;
    } elseif ($numCols > 9) {
        $fontSize = 7;
    } else {
        $fontSize = 8;
    }

    $colW = $usableWidth / $numCols;

    // Header row
    $pdf->SetFont('Arial', 'B', $fontSize);
    foreach ($columns as $c) {
        $pdf->Cell($colW, 7, cellText($c, 30), 1, 0, 'C');
    }
    $pdf->Ln();

    // Data rows
    $pdf->SetFont('Arial', '', $fontSize);

    if (count($rows) === 0) {
        $pdf->Cell(0, 7, "No rows found.", 1, 1, 'L');
        return;
    }

    foreach ($rows as $row) {
        if ($pdf->GetY() > ($pdf->GetPageHeight() - 25)) {
            $pdf->AddPage();

            // Repeat table header on new page
            $pdf->SetFont('Arial', 'B', $fontSize);
            foreach ($columns as $c) {
                $pdf->Cell($colW, 7, cellText($c, 30), 1, 0, 'C');
            }
            $pdf->Ln();

            $pdf->SetFont('Arial', '', $fontSize);
        }

        foreach ($columns as $c) {
            $val = $row[$c] ?? null;
            $pdf->Cell($colW, 6, cellText($val, 40), 1, 0, 'L');
        }
        $pdf->Ln();
    }
}

try {
    // Use PDO for reliability
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $pdf = new JasonPDF('L', 'mm', 'Letter'); // Landscape to fit more columns
    $pdf->AliasNbPages();
    $pdf->generatedAt = date("Y-m-d H:i:s");
    $pdf->SetTitle("Baseball_01 Database Report");
    $pdf->SetAuthor("Jason Luttrell");

    // ---------- Intro Page ----------
    $pdf->currentTable = "";
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "Baseball Database Report", 0, 1, 'L');

    $pdf->SetFont('Arial', '', 11);
    $intro = [
        "This PDF report was generated from the MySQL database '{$dbName}'.",
        "",
        "General topic context:",
        "Baseball databases commonly store structured information such as teams, players, games, line scores, batting/pitching statistics,",
        "and season summaries. These datasets are used for reporting, analytics, historical comparisons, and application features like",
        "rosters, leaderboards, and game logs.",
        "",
        "What follows:",
        "- A section for each table in the database",
        "- Each table includes a header row (column names) and all rows of data"
    ];

    foreach ($intro as $line) {
        $pdf->MultiCell(0, 6, $line, 0, 'L');
    }

    $pdf->Ln(4);

    // ---------- Get all tables ----------
    $tablesStmt = $pdo->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_NUM);

    if (count($tables) === 0) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "No tables found in database '{$dbName}'.", 0, 1, 'L');
    } else {
        foreach ($tables as $t) {
            $tableName = $t[0];

            // New page per table for clarity
            $pdf->currentTable = $tableName;
            $pdf->AddPage();

            $pdf->SetFont('Arial', 'B', 13);
            $pdf->Cell(0, 8, "Table: {$tableName}", 0, 1, 'L');
            $pdf->Ln(1);

            // Get columns
            $colsStmt = $pdo->query("DESCRIBE `{$tableName}`");
            $cols = [];
            foreach ($colsStmt as $colRow) {
                $cols[] = $colRow['Field'];
            }

            // Fetch all rows
            $dataStmt = $pdo->query("SELECT * FROM `{$tableName}`");
            $rows = $dataStmt->fetchAll();

            renderPdfTable($pdf, $cols, $rows);
        }
    }

    // Output PDF to browser
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=JasonBaseball_01_Report.pdf");
    $pdf->Output("I", "JasonBaseball_01_Report.pdf");

} catch (Exception $e) {
    // If PDF output already started, this won't render nicely—so keep it plain text.
    header("Content-Type: text/plain; charset=utf-8");
    echo "ERROR generating PDF.\n\n";
    echo $e->getMessage();
}