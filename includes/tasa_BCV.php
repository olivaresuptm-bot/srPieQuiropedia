<?php
function getDolarBCV() {
    // Usamos la API pública de DolarApi 
    $url = 'https://ve.dolarapi.com/v1/dolares/oficial';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // evitar bloqueos en InfinityFree
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
    
    // Fingimos ser un navegador estándar
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36');

    $respuesta = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    //conexión fue exitosa
    if ($respuesta !== false) {
        $datos = json_decode($respuesta, true);
        
        // La API devuelve el precio en el campo "promedio"
        if (isset($datos['promedio'])) {
            return (float) $datos['promedio'];
        }
    }
    
    return false;
}

// Obtenemos la tasa
$tasa_bcv = getDolarBCV();


if (!$tasa_bcv || $tasa_bcv <= 0) {
    $tasa_bcv = 0.10; // (Actualiza este número periódicamente por si acaso)
}
?>