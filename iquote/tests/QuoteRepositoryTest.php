<?php

namespace App\Tests;


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
            $this->onConsecutiveCalls($dirname.'/var', $dirname)
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
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testFindAuthor()
    {
        $dirname = \dirname(__DIR__);
        $kernel = $this->createMock(Kernel::class);
        $kernel->method('getProjectDir')->willReturn($dirname);
        $finder = new Finder();
        $repository = new QuoteRepository($kernel, $finder);

        // not existing author
        $result = $repository->findByAuthor('lorem');
        $this->assertIsArray($result);
        $this->assertCount(0, $result);

        // existing author
        $author = 'Steve Jobs';
        $result = $repository->findByAuthor($author);
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(1, $result);
        $this->assertEquals($author, $result[0]['author']);
    }
}
