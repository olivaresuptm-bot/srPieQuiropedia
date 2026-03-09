<?php
function getDolarBCV() {
    $url = 'https://alcambio.app/';
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/114.0.0.0 Safari/537.36\r\n",
            "timeout" => 8
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ];

    try {
        $context = stream_context_create($options);
        $html = @file_get_contents($url, false, $context);

        if ($html === false) return false; // Error de conexión

        if (preg_match('/<div id="dolar".*?<strong>\s*([\d,]+)\s*<\/strong>/s', $html, $matches)) {
            return (float) str_replace(',', '.', trim($matches[1]));
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}

$tasa_bcv = getDolarBCV();
?>