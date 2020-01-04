<?php

namespace App\Tests\Repository;


use App\Repository\QuoteRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;

class QuoteRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $dirname = \dirname(__DIR__);
        $kernel = $this->createMock(Kernel::class);
        $kernel->method('getProjectDir')->will(
            $this->onConsecutiveCalls($dirname.'/../var', $dirname.'/..')
        );
        $finder = new Finder();
        $repository = new QuoteRepository($kernel, $finder);

        // no data found
        $result = $repository->findAll();
        $this->assertIsArray($result);
        $this->assertCount(0, $result);

        // data found
        $result = $repository->findAll();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('quotes', $result);
    }

    public function testFindAuthor()
    {
        $author = 'Steve Jobs';
        $dirname = \dirname(__DIR__);
        $kernel = $this->createMock(Kernel::class);
        $realPath = $dirname.'/..';
        $kernel->method('getProjectDir')->will(
            $this->onConsecutiveCalls($dirname.'/../var', $realPath, $realPath)
        );
        $finder = new Finder();
        $repository = new QuoteRepository($kernel, $finder);

        // problem with json file
        $result = $repository->findByAuthor($author);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);

        // existing author
        $result = $repository->findByAuthor($author);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, $result);
        $this->assertEquals($author, $result[0]['author']);

        // not existing author
        $result = $repository->findByAuthor('lorem');
        $this->assertIsArray($result);
        $this->assertCount(0, $result);

    }
}
