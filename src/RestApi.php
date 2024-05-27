<?php

namespace Jdiaz\RestAPI;

class RestApi {

    public CONST POST = "POST";
    public CONST PUT = "PUT";
    public CONST GET = "GET";
    public CONST DELETE = "DELETE";
    public CONST JSON_CONTENT_TYPE = 'Content-Type: application/json';

    public function __invoke(string $url, ?array $header, array $params, array $body, string $contentType, string $requestType): array 
    {
        $curl = curl_init();

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
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
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $header ? $header : [$contentType]
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) { 
            print curl_error($curl); 
         } 

        curl_close($curl);

        return json_decode($response, true);
        
    }

}