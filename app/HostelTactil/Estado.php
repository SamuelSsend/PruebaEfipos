<?php

namespace App\HostelTactil;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\ConfigGeneral;

class Estado extends HostelTactil
{
    protected static function endpoint()
    {
        return "EstadoTPV";
    }

    public static function comprobarEstadoTPV()
    {
        try {
            $config = ConfigGeneral::first();

            $client = new Client();
            $uri = $config->hosteltactil_api . "/" . self::endpoint();

            $response = $client->get($uri, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config->hosteltactil_token,
                    'Accept' => 'application/json',
                    'IDLocal' => $config->hosteltactil_idlocal
                ]
            ]);

            // Check if the response status is 200
            if ($response->getStatusCode() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (RequestException $e) {
            Log::error('Failed fetching data from the API');
            Log::error($e);
            return false;
        }
    }
}