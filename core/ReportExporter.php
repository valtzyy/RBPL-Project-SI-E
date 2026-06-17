<?php

class ReportExporter
{
    public function buildPdf(string $reportType, array $rows, array $filters = []): string
    {
        $lines = [];
        $lines[] = 'REPORT: ' . strtoupper($reportType);

        if (($filters['start_date'] ?? '') !== '' || ($filters['end_date'] ?? '') !== '') {
            $lines[] = 'PERIODE: ' . ($filters['start_date'] ?: '-') . ' s/d ' . ($filters['end_date'] ?: '-');
        }

        $lines[] = 'DIBUAT: ' . date('Y-m-d H:i:s');
        $lines[] = '';

        if ($rows === []) {
            $lines[] = 'DATABASE KOSONG';
        } elseif ($this->isSectionedRows($rows)) {
            $hasContent = false;

            foreach ($rows as $sectionName => $sectionRows) {
                $lines[] = $this->humanizeSectionName((string) $sectionName);

                if (!is_array($sectionRows) || $sectionRows === []) {
                    $lines[] = 'DATABASE KOSONG';
                    $lines[] = '';
                    continue;
                }

                foreach ($sectionRows as $index => $row) {
                    $lines[] = 'BARIS ' . ($index + 1);
                    foreach ($row as $key => $value) {
                        $lines[] = strtoupper((string) $key) . ': ' . $this->stringifyValue($value);
                    }
                    $lines[] = '';
                }

                $hasContent = true;
            }

            if (!$hasContent) {
                $lines[] = 'DATABASE KOSONG';
            }
        } else {
            foreach ($rows as $index => $row) {
                $lines[] = 'BARIS ' . ($index + 1);
                foreach ($row as $key => $value) {
                    $lines[] = strtoupper((string) $key) . ': ' . $this->stringifyValue($value);
                }
                $lines[] = '';
            }
        }

        return $this->simplePdf($lines);
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
        $escapedLines = array_map(fn($line) => $this->escapePdfText($line), $lines);
        $content = 'BT /F1 10 Tf 40 800 Td 14 TL ';

        foreach ($escapedLines as $line) {
            $content .= '(' . $line . ') Tj T* ';
        }

        $content .= 'ET';

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Count 1 /Kids [3 0 R] >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = '5 0 obj << /Length ' . strlen($content) . ' >> stream' . "\n" . $content . "\n" . 'endstream endobj';

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
