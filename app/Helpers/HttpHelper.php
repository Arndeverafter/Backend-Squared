<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class HttpHelper
{

    /**
     * Create a new Client to Retrieve data from the given url
     *
     * Testing Guzzle Client https://docs.guzzlephp.org/en/latest/testing.html
     */
    public static function send($method, $requestUrl = '')
    {
        $client = new Client();

        $headers = ['Accept-Encoding' => 'gzip', 'Accept' => 'application/json'];

        $requestUrl = $requestUrl === '' ? CredentialsHelper::CUSTOMER_API_ENDPOINT : $requestUrl;
        try {
            $response = $client->request($method, $requestUrl, [
                'headers' => $headers,
                // 'http_errors' => false, : if you uncomment this then you cant use getStatusCode() cos the response would be a string
                'verify' => false, // Set FALSE due to restrictions but this should be changed to true ( which is the default)
            ]);
            return $response->getBody()->getContents();
        } catch (ClientException $e) {
            // Usually this is enough
            Log::error(Psr7\Message::toString($e->getRequest()));
            Log::error(Psr7\Message::toString($e->getResponse()));
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 490);
        }
        catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                Log::error(Psr7\Message::toString($e->getResponse()));
                return UtilsHelper::apiResponseConstruct('message', $response->getReasonPhrase(), $response->getStatusCode());
            }
        } catch (GuzzleException $e) {
            // but just in case
            Log::error($e->getMessage());
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 490);
        } catch (Exception $e) {
            // or if something unrelated to the client Request happened this should handle that
            Log::error($e->getMessage());
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 490);
        }
    }
}
