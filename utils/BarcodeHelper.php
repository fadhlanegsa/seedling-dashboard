<?php
/**
 * Smart Barcode Helper Class
 * Handles the generation, parsing, and offline validation of Smart Barcode Strings.
 * Format: [PREFIX]-[BATCH_ID]-[BPDAS_ID]-[NURSERY_ID]-[SEED_SOURCE_ID]-[SEEDLING_TYPE_ID]-[SOWING_DATE_YYMMDD]-[INDEX]
 * Example: PE-45-3-12-7-42-260415-88
 */

class BarcodeHelper {
    
    /**
     * Generate a Smart Barcode string
     * 
     * @param string $type Prefix ('PE' or 'ET')
     * @param int $batchId Weaning or Entres Primary Key ID
     * @param int $bpdasId BPDAS ID
     * @param int $nurseryId Nursery ID
     * @param int $seedSourceId Seed Source ID
     * @param int $seedlingTypeId Seedling Type ID
     * @param string $sowingDate Date of sowing (YYYY-MM-DD)
     * @param int $index Individual seedling number
     * @return string
     */
    public static function generate($type, $batchId, $bpdasId, $nurseryId, $seedSourceId, $seedlingTypeId, $sowingDate, $index) {
        $type = strtoupper($type);
        if (!in_array($type, ['PE', 'ET'])) {
            $type = 'PE';
        }
        
        $dateFormatted = '000000';
        if (!empty($sowingDate)) {
            $timestamp = strtotime($sowingDate);
            if ($timestamp !== false) {
                $dateFormatted = date('ymd', $timestamp);
            }
        }
        
        return sprintf('%s-%d-%d-%d-%d-%d-%s-%d', 
            $type,
            (int)$batchId,
            (int)$bpdasId,
            (int)$nurseryId,
            (int)$seedSourceId,
            (int)$seedlingTypeId,
            $dateFormatted,
            (int)$index
        );
    }
    
    /**
     * Parse a Smart Barcode string into structured array components
     * 
     * @param string $code Smart Barcode string
     * @return array|null Null if format is invalid
     */
    public static function parse($code) {
        if (empty($code)) {
            return null;
        }
        
        $parts = explode('-', trim($code));
        if (count($parts) !== 8) {
            return null;
        }
        
        $type = strtoupper($parts[0]);
        if (!in_array($type, ['PE', 'ET'])) {
            return null;
        }
        
        // Validate YYMMDD sowing date format (6 characters, numeric)
        $sowingDatePart = $parts[6];
        if (strlen($sowingDatePart) !== 6 || !is_numeric($sowingDatePart)) {
            return null;
        }
        
        // Try to reconstruct YYYY-MM-DD from YYMMDD
        $year = substr($sowingDatePart, 0, 2);
        $month = substr($sowingDatePart, 2, 2);
        $day = substr($sowingDatePart, 4, 2);
        
        $fullYear = (int)$year > 50 ? '19' . $year : '20' . $year;
        $sowingDateReconstructed = "{$fullYear}-{$month}-{$day}";
        
        return [
            'type' => $type,
            'batch_id' => (int)$parts[1],
            'bpdas_id' => (int)$parts[2],
            'nursery_id' => (int)$parts[3],
            'seed_source_id' => (int)$parts[4],
            'seedling_type_id' => (int)$parts[5],
            'sowing_date_raw' => $sowingDatePart,
            'sowing_date' => $sowingDateReconstructed,
            'index' => (int)$parts[7]
        ];
    }
}
