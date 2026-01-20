<?php
// util/upload_handler.php - Zentrale Upload-Behandlung

class UploadHandler {
    
    // Upload-Typen mit Konfiguration
    private static $types = [
        'course_material' => [
            'dir' => '/uploads/courses/',
            'max_size' => 5 * 1024 * 1024, // 5 MB
            'allowed_types' => ['application/pdf', 'application/msword', 
                              'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                              'image/jpeg', 'image/png', 'image/gif']
        ],
        'profile_image' => [
            'dir' => '/uploads/profiles/',
            'max_size' => 2 * 1024 * 1024, // 2 MB
            'allowed_types' => ['image/jpeg', 'image/png']
        ]
    ];
    
    /**
     * Validiert eine hochgeladene Datei
     */
    public static function validate($file, $type = 'course_material') {
        $config = self::$types[$type] ?? null;
        
        if (!$config) {
            return ['success' => false, 'error' => 'Unbekannter Upload-Typ'];
        }
        
        // Keine Datei?
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'Keine Datei ausgewählt'];
        }
        
        // Upload-Fehler?
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Fehler beim Upload'];
        }
        
        // Dateigröße?
        if ($file['size'] > $config['max_size']) {
            $max_mb = $config['max_size'] / 1024 / 1024;
            return ['success' => false, 'error' => "Datei zu groß (max: {$max_mb} MB)"];
        }
        
        // MIME-Type?
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $config['allowed_types'])) {
            return ['success' => false, 'error' => 'Dateityp nicht erlaubt'];
        }
        
        return ['success' => true, 'mime_type' => $mime_type];
    }
    
    /**
     * Speichert eine Datei sicher
     */
    public static function save($file, $type = 'course_material', $subdirectory = '') {
        $config = self::$types[$type] ?? null;
        
        if (!$config) {
            return ['success' => false, 'error' => 'Unbekannter Upload-Typ'];
        }
        
        // Validierung
        $validation = self::validate($file, $type);
        if (!$validation['success']) {
            return $validation;
        }
        
        // Verzeichnis erstellen
        $upload_dir = __DIR__ . '/..' . $config['dir'] . $subdirectory;
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                return ['success' => false, 'error' => 'Verzeichnis konnte nicht erstellt werden'];
            }
        }
        
        // Dateiname sanitizen
        $original_name = basename($file['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        
        // Eindeutigen Dateinamen generieren
        $unique_filename = uniqid() . '_' . time() . '.' . $ext;
        $file_path = $upload_dir . $unique_filename;
        
        // Datei verschieben
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return ['success' => false, 'error' => 'Fehler beim Speichern der Datei'];
        }
        
        return [
            'success' => true,
            'filename' => $unique_filename,
            'original_name' => $original_name,
            'size' => $file['size'],
            'mime_type' => $validation['mime_type'],
            'public_path' => $config['dir'] . $subdirectory . $unique_filename
        ];
    }
    
    /**
     * Löscht eine Datei
     */
    public static function delete($file_path, $type = 'course_material') {
        $config = self::$types[$type] ?? null;
        
        if (!$config) {
            return false;
        }
        
        $full_path = __DIR__ . '/..' . $file_path;
        
        if (file_exists($full_path)) {
            return unlink($full_path);
        }
        
        return false;
    }
    
    /**
     * Gibt erlaubte Dateitypen als String zurück
     */
    public static function getAllowedExtensions($type = 'course_material') {
        $config = self::$types[$type] ?? null;
        
        if (!$config) {
            return '';
        }
        
        $extensions = [];
        $mime_map = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg,jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];
        
        foreach ($config['allowed_types'] as $mime) {
            if (isset($mime_map[$mime])) {
                $extensions[] = $mime_map[$mime];
            }
        }
        
        return implode(',', $extensions);
    }
}
?>
