<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertGreaterThan(1, json_decode($content, true));
    }

    public function testQuoteLimit()
    {
        $client = static::createClient();
        $client->request('GET', '/lorem?limit=11');
        $this->assertResponseStatusCodeSame(400);
        $content = $client->getResponse()->getContent();
        $this->assertEquals('"Quote limit is 10"', $content);
    }

    public function testShow()
    {
        $client = static::createClient();

        $client->request('GET', '/Maya Angelou');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertStringContainsStringIgnoringCase('people will forget what you did', $content);

        $client->request('GET', '/Maya Angelou?limit=2');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertCount(2, json_decode($content, true));
    }
}
