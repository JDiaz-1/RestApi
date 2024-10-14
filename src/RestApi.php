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

    public static function send(string $url, ?array $header, array $params, array $body, string $contentType, string $requestType): array
    {
        $curl = curl_init();

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $payload = http_build_query($body);

        if ($contentType == self::JSON_CONTENT_TYPE) {
            $payload = json_encode($body);
        }

        if ($contentType == self::WWW_FORM_URLENCODED_TYPE) {
            $payload = http_build_query($body);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $header ? $header : [$contentType],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            print curl_error($curl);
        }

        curl_close($curl);

        return json_decode($response, true);

    }

}
