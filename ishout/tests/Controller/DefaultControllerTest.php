<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $quotes = json_decode($content, true);
        $this->assertGreaterThan(1, $quotes);
        $quote = $quotes['quotes'][0]['quote'];
        $this->assertContains('ABOUT GETTING AND HAVING', $quote);
        $this->assertStringEndsWith('!', $quote);
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

        // test correct result
        $client->request('GET', '/maya-angelou');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertContains('PEOPLE WILL FORGET WHAT YOU DID', $content);
        $quotes = json_decode($content, true);
        $this->assertStringEndsWith('!', $quotes[0]['quote']);

        // test limit
        $client->request('GET', '/maya-angelou?limit=2');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertCount(2, json_decode($content, true));

        // no results
        $client->request('GET', '/lorem-ipsum');
        $this->assertResponseStatusCodeSame(204);
        $content = $client->getResponse()->getContent();
        $this->assertEmpty($content);
    }
}
