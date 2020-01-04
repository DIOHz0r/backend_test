<?php

namespace App\Controller;

use App\Helper\TransformHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /** KernelInterface $appKernel */
    private $appKernel;
    /**
     * @var TransformHelper
     */
    private $transformHelper;

    public function __construct(KernelInterface $appKernel, TransformHelper $transformHelper)
    {
        $this->appKernel = $appKernel;
        $this->transformHelper = $transformHelper;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index()
    {
        return new JsonResponse($this->findAll());
    }

    /**
     * @Route("/{author}", name="show_author", methods={"GET"}, requirements={"author"="\w+"})
     * @param string $author
     * @param Request $request
     * @return JsonResponse
     */
    public function show(string $author, Request $request)
    {
        $author = $this->fixAuthorName($author);
        $limit = $request->query->get('limit');
        if ($limit > 10) {
            return new JsonResponse('Quote limit is 10', 400);
        }

        $result = $this->findAll();
        $data = [];
        foreach ($result['quotes'] as $list => $item) {
            if (!stripos($author, $item['author'])) {
                continue;
            }

            $data[] = $this->transformHelper($item['quote']);
        }
        return new JsonResponse($data);
    }

    /**
     * @return mixed
     */
    private function findAll()
    {
        $data = null;
        $sourceDir = $this->appKernel->getProjectDir() . '/../';
        $finder = new Finder();
        $finder->files()->name('quotes.json')->in($sourceDir);

        if (!$finder->hasResults()) {
            return json_decode($data, true);
        }

        foreach ($finder as $file) {
            $data = $file->getContents();
        }
        return json_decode($data, true);
    }

    public function fixAuthorName($string)
    {
        return ucwords(str_replace('-', ' ', $string));
    }
}
