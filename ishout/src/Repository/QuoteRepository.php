<?php


namespace App\Repository;


use App\Helper\TransformHelper;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class QuoteRepository
{

    /**
     * @var Finder
     */
    private $finder;
    /**
     * @var KernelInterface
     */
    private $appKernel;

    public function __construct(KernelInterface $appKernel, Finder $finder)
    {
        $this->appKernel = $appKernel;
        $this->finder = $finder;
    }


    /**
     * @return array|null
     */
    public function findAll(): ?array
    {
        $data = [];
        $finder = $this->readQuotes();

        if (!$finder->hasResults()) {
            return json_decode('{}', true);
        }

        foreach ($finder as $file) {
            $data = $file->getContents();
        }

        return json_decode($data, true);
    }

    /**
     * @param string $author
     * @return array
     */
    public function findByAuthor(string $author): array
    {
        $data = [];
        $result = $this->findAll();
        if (!key_exists('quotes', $result)) {
            return $data;
        }

        foreach ($result['quotes'] as $key => $item) {
            if (TransformHelper::slugify($item['author']) != TransformHelper::slugify($author)) {
                continue;
            }
            $data[] = $item['quote'];
        }
        return $data;
    }

    /**
     * @return Finder
     */
    private function readQuotes(): Finder
    {
        $adapter = new ArrayAdapter();

        $sourceDir = $this->appKernel->getProjectDir().'/../';
        $cache = $adapter->getItem('quotes_json_'.md5($sourceDir));
        if (!$cache->isHit()) {
            $finder = $this->finder;
            $finder->files()->name('quotes.json')->in($sourceDir);
            $cache->expiresAfter(60);
            $adapter->save($cache->set($finder));
        }

        return $cache->get();
    }
}