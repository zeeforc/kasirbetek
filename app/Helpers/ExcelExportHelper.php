<?php
namespace App\Helpers;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell as CellEntity;
class ExcelExportHelper
{
    public static function generateExcel(array $data, string $fileName): string
    {
        $fileName = $fileName . '.xlsx';
        $path = 'reports/' . $fileName;
        $pathDirectory = storage_path('app/public/reports');
        if (!file_exists($pathDirectory)) {
            mkdir($pathDirectory, 0755, true);
        }
        $writer = new Writer();
        $fullPath = storage_path('app/public/' . $path);
        $writer->openToFile($fullPath);
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $headerCells = array_map(fn($header) => CellEntity::fromValue($header), $headers);
            $writer->addRow(new Row($headerCells));
            foreach ($data as $row) {
                $cells = array_map(fn($value) => CellEntity::fromValue($value), $row);
                $writer->addRow(new Row($cells));
            }
        }
        $writer->close();
        return $path;
    }
}
