<?php
/**
 * User: Zhanybek Seitaliev
 * Date: 11.02.2019
 * Time: 19:45
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ShopfiySdkTest extends TestCase
{

    public function testGenerateInstallUrl(): void
    {

        $faker = Faker\Factory::create();

        $myshopifyDomain = $faker->url;
        $apiKey = $faker->uuid;
        $scopes = $faker->text(50);
        $redirectUrl = $faker->url;

        $generatedInstallUrl = \Improck\Shopify\Shopify::generateInstallUrl(
            $myshopifyDomain,
            $apiKey,
            $scopes,
            $redirectUrl
        );

        $actual = "https://$myshopifyDomain/admin/oauth/authorize?"
            ."client_id=".$apiKey
            ."&scope=".$scopes
            ."&redirect_uri=".$redirectUrl
            ."&state={nonce}"
            ."&grant_options[]={option}";

        $this->assertEquals(
            $generatedInstallUrl,
            $actual
        );
    }

    public function testGetMyshopifyDomain()
    {

        $faker = Faker\Factory::create();

        $myshopifyDomain = $faker->url;
        $accessToken = $faker->uuid;

        $shopify = new \Improck\Shopify\Shopify($myshopifyDomain, $accessToken);

        $this->assertEquals(
            $shopify->getMyshopifyDomain(),
            $myshopifyDomain
        );

    }

    public function testGetAccessToken()
    {

        $faker = Faker\Factory::create();

        $myshopifyDomain = $faker->url;
        $accessToken = $faker->uuid;

        $shopify = new \Improck\Shopify\Shopify($myshopifyDomain, $accessToken);

        $this->assertEquals(
            $shopify->getAccessToken(),
            $accessToken
        );

    }
}

