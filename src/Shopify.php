<?php
/**
 * User: Zhanybek Seitaliev
 * Date: 11.02.2019
 * Time: 17:04
 */

namespace Improck\Shopify;

use GuzzleHttp\Client;
use Tightenco\Collect\Support\Collection;

class Shopify
{

    private $myshopifyDomain;

    private $accessToken;

    private $client;

    public function __construct(string $myshopifyDomain, string $accessToken)
    {
        $this->myshopifyDomain = $myshopifyDomain;
        $this->accessToken = $accessToken;

        $this->client = new Client([
            "base_uri" => "https://$myshopifyDomain",
            'headers' => [
                'X-Shopify-Access-Token' => $this->accessToken,
                'X-Frame-Options' => 'allow'
            ],
        ]);
    }

    /**
     * Generate install url
     *
     * @param string $myshopifyDomain
     * @param string $apiKey
     * @param string $scopes
     * @param string $redirectUrl
     * @return string
     */
    public static function generateInstallUrl(
        string $myshopifyDomain,
        string $apiKey,
        string $scopes,
        string $redirectUrl
    ): string
    {
        return "https://$myshopifyDomain/admin/oauth/authorize?"
            ."client_id=".$apiKey
            ."&scope=".$scopes
            ."&redirect_uri=".$redirectUrl
            ."&state={nonce}"
            ."&grant_options[]={option}";
    }

    public function getAccessToken():string
    {

        return $this->accessToken;

    }

    public function getMyshopifyDomain():string
    {

        return $this->myshopifyDomain;

    }

    public static function authorize(
        string $myshopifyDomain,
        string $code,
        string $apiKey,
        string $secretKey
    ):self
    {
        $client = new Client(["base_url" => "https://$myshopifyDomain"]);

        $response = $client->post("https://$myshopifyDomain/admin/oauth/access_token", [
            "form_params" => [
                "client_id" =>  $apiKey,
                "client_secret" => $secretKey,
                "code" => $code
            ]
        ]);

        $accessToken = json_decode($response->getBody()->getContents(), true)['access_token'];

        return new self($myshopifyDomain, $accessToken);
    }

    /**
     * Making get api call
     * Resource string must without "/admin"
     * Example: "products.json"
     *
     * @param string $resourceUrl
     * @return Collection
     */
    public function get(string $resourceUrl):Collection
    {

        $response = $this->client->get("/admin/$resourceUrl");

        $collection = $this->makeContentToCollection($response->getBody()->getContents());

        return $collection;

    }

    /**
     * Get resource count
     *
     * Resource name example: "products"
     *
     * @param string $resourceName
     * @return int
     */
    public function getCount(string $resourceName):int
    {
        $count = $this->get("$resourceName/count.json")[0];

        return (int)$count;
    }

    /**
     * Making post api call
     * Resource string must without "/admin"
     * Example: "webhooks.json"
     *
     * @param string $resourceUrl
     * @param array $postParam
     * @return Collection
     */
    public function post(string $resourceUrl, array $postParam):Collection
    {
        $response = $this->client->post("/admin/$resourceUrl", $postParam);

        $collection = $this->makeContentToCollection($response->getBody()->getContents());

        return $collection;
    }

    /**
     * Making put api call
     * Resource string must without "/admin"
     * Example: "products.json"
     *
     * @param string $resourceUrl
     * @param array $postParam
     * @return Collection
     */
    public function put(string $resourceUrl, array $postParam):Collection
    {
        $response = $this->client->put("/admin/$resourceUrl", $postParam);

        $collection = $this->makeContentToCollection($response->getBody()->getContents());

        return $collection;
    }

    /**
     * Making delete api call
     *
     * @param string $resourceUrl
     * @return int statusCode
     */
    public function delete(string $resourceUrl)
    {
        $response = $this->client->delete("/admin/$resourceUrl");

        $status = $response->getStatusCode();

        return $status;

    }


    /**
     * Get all resources
     *
     * @param string $resourceUrl
     * @return Collection
     */
    public function getFromAllPages(string $resourceUrl):Collection
    {
        $resourceName = $this->getResourceName($resourceUrl);

        $count = $this->getCount($resourceName);

        $pageCount = ceil($count / 250);

        $collections = [];

        for ($i = 1; $i <= $pageCount; $i++) {

            $collections[] = $this->get($resourceUrl . "?page=$i&limit=250");

        }

        return collect($collections)->collapse();

    }

    /**
     * Converting resource result to collection
     *
     * @param string $content
     * @return Collection
     */
    private function makeContentToCollection(string $content):Collection
    {

        $jsonDecoded = json_decode($content, true);

        $resourceKey = key($jsonDecoded);

        $collection = new Collection($jsonDecoded[$resourceKey]);

        return $collection;

    }

    private function getResourceName(string $resourceUrl):string
    {
        return strstr($resourceUrl, '.json', true);
    }
}