<?php
/**
 * View Helper Functions
 * Safe array access and common view utilities
 */

/**
 * Safely get array value with default
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function safe_get($array, $key, $default = '') {
    return $array[$key] ?? $default;
}

/**
 * Safely get and escape HTML
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return string
 */
function safe_html($array, $key, $default = '') {
    return htmlspecialchars($array[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

/**
 * Safely format number
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return string
 */
function safe_number($array, $key, $default = 0) {
    return formatNumber($array[$key] ?? $default);
}

/**
 * Safely format date
 * 
 * @param array $array
 * @param string $key
 * @param string $format
 * @param mixed $default
 * @return string
 */
function safe_date($array, $key, $format = DATE_FORMAT, $default = '') {
    return isset($array[$key]) ? formatDate($array[$key], $format) : $default;
}

/**
 * Get status badge class
 * 
 * @param string $status
 * @return string
 */
function status_badge_class($status) {
    $classes = [
        'pending' => 'warning',
        'approved' => 'purple',
        'rejected' => 'danger',
        'delivered' => 'delivered',
        'active' => 'success',
        'inactive' => 'secondary'
    ];
    return $classes[$status] ?? 'secondary';
}

/**
 * Get status text
 * 
 * @param string $status
 * @return string
 */
function status_text($status) {
    $texts = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'delivered' => 'Sudah Diserahkan',
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif'
    ];
    return $texts[$status] ?? ucfirst($status);
}

/**
 * Get status icon
 * 
 * @param string $status
 * @return string
 */
function status_icon($status) {
    $icons = [
        'pending' => 'clock',
        'approved' => 'check-circle',
        'rejected' => 'times-circle',
        'delivered' => 'truck',
        'active' => 'check',
        'inactive' => 'times'
    ];
    return $icons[$status] ?? 'circle';
}
