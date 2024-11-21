<?php

namespace Jdiaz\Rest;

class RestApi
{
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const GET = 'GET';
    public const DELETE = 'DELETE';

    public const JSON_CONTENT_TYPE = 'Content-Type: application/json';
    public const WWW_FORM_URLENCODED_TYPE = 'Content-Type: application/x-www-form-urlencoded';

    public static function send(
        string $url,
        ?array $header,
        array $params = [],
        array $body = [],
        string $contentType = self::JSON_CONTENT_TYPE,
        string $requestType = self::POST
    ): array {
        $curl = curl_init();

        // Construir la URL solo si es GET o DELETE
        if (in_array($requestType, [self::GET, self::DELETE]) && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        // Configurar el cuerpo de la solicitud
        $payload = null;
        if (in_array($requestType, [self::POST, self::PUT])) {
            if ($contentType === self::JSON_CONTENT_TYPE) {
                $payload = json_encode($body);
            } elseif ($contentType === self::WWW_FORM_URLENCODED_TYPE) {
                $payload = http_build_query($body);
            }
        }

        // Configurar encabezados
        $httpHeader = $header ?? [];
        $httpHeader[] = $contentType;

        // Configurar cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $httpHeader,
        ]);

        // Ejecutar la solicitud
        $response = curl_exec($curl);

        // Manejar errores de cURL
        if (curl_errno($curl)) {
            throw new \RuntimeException('cURL Error: ' . curl_error($curl));
        }

        // Obtener código de respuesta HTTP
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Verificar códigos de respuesta HTTP no exitosos
        if ($httpCode >= 400) {
            throw new \RuntimeException("HTTP Error: $httpCode, Response: $response");
        }

        // Decodificar la respuesta JSON
        return json_decode($response, true) ?? [];
    }
}
