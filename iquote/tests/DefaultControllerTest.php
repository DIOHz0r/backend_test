<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;

class DefaultControllerTest extends WebTestCase
{
    protected function getJsonSource()
    {
        self::bootKernel();

        $content = '';
        $sourceDir = self::$kernel->getProjectDir() . '/../';
        $finder = new Finder();
        $finder->files()->name('quotes.json')->in($sourceDir);
        $this->assertTrue($finder->hasResults());
        foreach ($finder as $file) {
            $content = $file->getContents();
        }
        return $content;
    }

    public function testIndex()
    {
        $client = static::createClient();
        $data = $this->getJsonSource();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $realJson = json_encode(json_decode($data, true));
        $this->assertJsonStringEqualsJsonString($realJson, $content);
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

        $client->request('GET', '/chinese-proverb');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertContains('"The best time to plant a tree was 20 years ago. The second best time is now."', $content);

        $client->request('GET', '/chinese-proverb?limit=3');
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertCount(3, $content);
    }
}
