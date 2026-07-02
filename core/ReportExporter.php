<?php

class ReportExporter
{
    public function buildPdf(string $reportType, array $rows, array $filters = []): string
    {
        $stream = "";
        
        // Define color palette (Slate Blue Theme)
        // Header banner background: 0.18 0.24 0.35
        $titleText = "REPORT: " . strtoupper(str_replace('_', ' ', $reportType));
        
        $stream .= "0.18 0.24 0.35 rg\n";
        $stream .= "40 520 762 45 re f\n"; // Header rectangle
        
        $stream .= "BT\n";
        $stream .= "  /F2 16 Tf\n"; // Bold
        $stream .= "  1 1 1 rg\n"; // White text
        $stream .= "  55 535 Td\n";
        $stream .= "  (" . $this->escapePdfText($titleText) . ") Tj\n";
        $stream .= "ET\n";
        
        // Metadata below banner
        $stream .= "0 0 0 rg\n"; // Black text
        $stream .= "BT\n";
        $stream .= "  /F1 9 Tf\n"; // Regular
        $stream .= "  45 495 Td\n";
        $stream .= "  (Generated: " . date('Y-m-d H:i:s') . ") Tj\n";
        $stream .= "ET\n";
        
        if (($filters['start_date'] ?? '') !== '' || ($filters['end_date'] ?? '') !== '') {
            $periodText = "Period: " . ($filters['start_date'] ?: '-') . " to " . ($filters['end_date'] ?: '-');
            $stream .= "BT\n";
            $stream .= "  /F1 9 Tf\n";
            $stream .= "  300 495 Td\n";
            $stream .= "  (" . $this->escapePdfText($periodText) . ") Tj\n";
            $stream .= "ET\n";
        }
        
        // Line separator below metadata
        $stream .= "0.8 0.8 0.8 RG\n";
        $stream .= "40 485 m 802 485 l S\n";
        
        $y = 460;
        
        if ($rows === []) {
            $stream .= "BT\n";
            $stream .= "  /F2 12 Tf\n";
            $stream .= "  0.5 0.5 0.5 rg\n";
            $stream .= "  45 " . $y . " Td\n";
            $stream .= "  (DATABASE KOSONG / TIDAK ADA DATA) Tj\n";
            $stream .= "ET\n";
        } else {
            $sections = [];
            if ($this->isSectionedRows($rows)) {
                $sections = $rows;
            } else {
                $sections = [$reportType => $rows];
            }
            
            foreach ($sections as $sectionName => $sectionRows) {
                // Section Title
                $stream .= "BT\n";
                $stream .= "  /F2 11 Tf\n";
                $stream .= "  0.18 0.24 0.35 rg\n";
                $stream .= "  45 " . ($y - 5) . " Td\n";
                $stream .= "  (" . $this->escapePdfText($this->humanizeSectionName($sectionName)) . ") Tj\n";
                $stream .= "ET\n";
                $y -= 15;
                
                if (!is_array($sectionRows) || $sectionRows === []) {
                    $stream .= "BT\n";
                    $stream .= "  /F1 9 Tf\n";
                    $stream .= "  0.5 0.5 0.5 rg\n";
                    $stream .= "  55 " . ($y - 10) . " Td\n";
                    $stream .= "  (Tidak ada data) Tj\n";
                    $stream .= "ET\n";
                    $y -= 25;
                    continue;
                }
                
                $headers = array_keys($sectionRows[0]);
                $colCount = count($headers);
                $colWidth = 762 / $colCount;
                
                // Draw Header Background
                $stream .= "0.85 0.87 0.9 rg\n";
                $stream .= "40 " . ($y - 20) . " " . 762 . " 20 re f\n";
                
                // Draw Header Text
                $stream .= "0.1 0.15 0.25 rg\n";
                $stream .= "BT\n";
                $stream .= "  /F2 8 Tf\n"; // Bold header
                for ($i = 0; $i < $colCount; $i++) {
                    $headerName = strtoupper(str_replace('_', ' ', $headers[$i]));
                    if (strlen($headerName) > 15) {
                        $headerName = substr($headerName, 0, 13) . "..";
                    }
                    $xOffset = 40 + ($i * $colWidth) + 5;
                    $stream .= $xOffset . " " . ($y - 14) . " Td (" . $this->escapePdfText($headerName) . ") Tj\n";
                    $stream .= "-" . ($i * $colWidth + 5) . " 0 Td\n";
                }
                $stream .= "ET\n";
                
                // Draw border for header
                $stream .= "0.7 0.7 0.7 RG\n";
                $stream .= "40 " . ($y - 20) . " " . 762 . " 20 re S\n";
                
                $y -= 20;
                
                // Draw Rows
                $rowIdx = 0;
                foreach ($sectionRows as $row) {
                    if ($y < 60) {
                        $stream .= "BT\n";
                        $stream .= "  /F2 8 Tf\n";
                        $stream .= "  0.4 0.4 0.4 rg\n";
                        $stream .= "  45 " . ($y - 12) . " Td\n";
                        $stream .= "  (... Laporan dipotong karena batasan halaman PDF ...) Tj\n";
                        $stream .= "ET\n";
                        $y -= 15;
                        break;
                    }
                    
                    // Zebra stripe background
                    if ($rowIdx % 2 === 1) {
                        $stream .= "0.96 0.97 0.98 rg\n";
                        $stream .= "40 " . ($y - 16) . " " . 762 . " 16 re f\n";
                    }
                    
                    // Draw cell text
                    $stream .= "0.1 0.1 0.1 rg\n";
                    $stream .= "BT\n";
                    $stream .= "  /F1 8 Tf\n";
                    for ($i = 0; $i < $colCount; $i++) {
                        $valStr = $this->stringifyValue($row[$headers[$i]]);
                        if (is_numeric($valStr) && (str_contains($headers[$i], 'amount') || str_contains($headers[$i], 'price') || str_contains($headers[$i], 'total') || str_contains($headers[$i], 'paid'))) {
                            $valStr = "Rp " . number_format((float)$valStr, 0, ',', '.');
                        }
                        $maxChars = (int)($colWidth / 5.5);
                        if (strlen($valStr) > $maxChars) {
                            $valStr = substr($valStr, 0, $maxChars - 2) . "..";
                        }
                        $xOffset = 40 + ($i * $colWidth) + 5;
                        $stream .= $xOffset . " " . ($y - 12) . " Td (" . $this->escapePdfText($valStr) . ") Tj\n";
                        $stream .= "-" . ($i * $colWidth + 5) . " 0 Td\n";
                    }
                    $stream .= "ET\n";
                    
                    // Draw row bottom line
                    $stream .= "0.85 0.85 0.85 RG\n";
                    $stream .= "40 " . ($y - 16) . " m 802 " . ($y - 16) . " l S\n";
                    
                    $y -= 16;
                    $rowIdx++;
                }
                
                $y -= 25;
            }
        }
        
        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Count 1 /Kids [3 0 R] >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> endobj';
        $objects[] = '6 0 obj << /Length ' . strlen($stream) . ' >> stream' . "\n" . $stream . "\n" . 'endstream endobj';
        
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }
        
        $xrefOffset = strlen($pdf);
        $pdf .= 'xref' . "\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
        }
        
        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= '%%EOF';
        
        return $pdf;
    }

    public function buildSpreadsheet(string $reportType, array $rows, array $filters = []): array
    {
        $filenameBase = $reportType . '-report-' . date('Ymd-His');

        if (class_exists('ZipArchive')) {
            return [
                $this->buildXlsx($reportType, $rows, $filters),
                $filenameBase . '.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
        }

        return [
            $this->buildSpreadsheetXml($reportType, $rows, $filters),
            $filenameBase . '.xls',
            'application/vnd.ms-excel',
        ];
    }

    private function buildXlsx(string $reportType, array $rows, array $filters): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'rbpl_xlsx_');
        $zip = new ZipArchive();
        $zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $sheetRows = $this->spreadsheetRows($reportType, $rows, $filters);
        $sheetXmlRows = '';
        foreach ($sheetRows as $rowIndex => $row) {
            $sheetXmlRows .= '<row r="' . ($rowIndex + 1) . '">';
            foreach ($row as $colIndex => $cellValue) {
                $cellRef = $this->xlsxColumnName($colIndex + 1) . ($rowIndex + 1);
                $escaped = htmlspecialchars($cellValue, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $sheetXmlRows .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }
            $sheetXmlRows .= '</row>';
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($sheetXmlRows));
        $zip->close();

        $content = (string) file_get_contents($tmpFile);
        @unlink($tmpFile);

        return $content;
    }

    private function buildSpreadsheetXml(string $reportType, array $rows, array $filters): string
    {
        $sheetRows = $this->spreadsheetRows($reportType, $rows, $filters);
        $xmlRows = '';

        foreach ($sheetRows as $row) {
            $xmlRows .= '<Row>';
            foreach ($row as $cellValue) {
                $escaped = htmlspecialchars($cellValue, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $xmlRows .= '<Cell><Data ss:Type="String">' . $escaped . '</Data></Cell>';
            }
            $xmlRows .= '</Row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            . '<Worksheet ss:Name="Report"><Table>'
            . $xmlRows
            . '</Table></Worksheet></Workbook>';
    }

    private function spreadsheetRows(string $reportType, array $rows, array $filters): array
    {
        $output = [];
        $output[] = ['Report Type', strtoupper($reportType)];
        $output[] = ['Generated At', date('Y-m-d H:i:s')];
        $output[] = ['Start Date', $filters['start_date'] ?? ''];
        $output[] = ['End Date', $filters['end_date'] ?? ''];
        $output[] = [''];

        if ($rows === []) {
            $output[] = ['DATABASE KOSONG'];
            return $output;
        }

        if ($this->isSectionedRows($rows)) {
            $hasContent = false;

            foreach ($rows as $sectionName => $sectionRows) {
                $output[] = [$this->humanizeSectionName((string) $sectionName)];

                if (!is_array($sectionRows) || $sectionRows === []) {
                    $output[] = ['DATABASE KOSONG'];
                    $output[] = [''];
                    continue;
                }

                $headers = array_keys($sectionRows[0]);
                $output[] = $headers;

                foreach ($sectionRows as $row) {
                    $output[] = array_map(fn($value) => $this->stringifyValue($value), $row);
                }

                $output[] = [''];
                $hasContent = true;
            }

            if (!$hasContent) {
                $output[] = ['DATABASE KOSONG'];
            }

            return $output;
        }

        $headers = array_keys($rows[0]);
        $output[] = $headers;

        foreach ($rows as $row) {
            $output[] = array_map(fn($value) => $this->stringifyValue($value), $row);
        }

        return $output;
    }

    private function simplePdf(array $lines): string
    {
        return '';
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Report" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    private function sheetXml(string $sheetXmlRows): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetXmlRows . '</sheetData>'
            . '</worksheet>';
    }

    private function xlsxColumnName(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function stringifyValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    private function isSectionedRows(array $rows): bool
    {
        if ($rows === []) {
            return false;
        }

        $keys = array_keys($rows);
        $hasStringKey = array_filter($keys, 'is_string') !== [];

        if (!$hasStringKey) {
            return false;
        }

        foreach ($rows as $value) {
            if (!is_array($value)) {
                return false;
            }

            if ($value === []) {
                continue;
            }

            $firstNestedValue = reset($value);
            return is_array($firstNestedValue);
        }

        return true;
    }

    private function humanizeSectionName(string $value): string
    {
        return strtoupper(str_replace('_', ' ', $value));
    }

    private function escapePdfText(string $value): string
    {
        return str_replace(
            ['\\', '(', ')', "\r", "\n", "\t"],
            ['\\\\', '\(', '\)', '', '', ' '],
            $value
        );
    }
}
