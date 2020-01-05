<?php

namespace App\Controller;

use App\Helper\TransformHelper;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{

    /**
     * @var TransformHelper
     */
    private $transformHelper;
    /**
     * @var QuoteRepository
     */
    private $repository;

    public function __construct(QuoteRepository $repository, TransformHelper $transformHelper)
    {
        $this->repository = $repository;
        $this->transformHelper = $transformHelper;
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index()
    {
        $result = $this->repository->findAll();
        $data = [];
        if (!key_exists('quotes', $result)) {
            return new JsonResponse($data, 204);
        }
        $transformHelper = $this->transformHelper;
        foreach ($result['quotes'] as $key => $quote) {
            $author = $quote['author'];
            $data[$author]['quotes'][] = $transformHelper($quote['quote']);
            $data[$author]['_links'] = $this->generateUrl(
                'show_author',
                ['author' => $this->slugify($author)],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        ksort($data);

        return new JsonResponse($data);
    }

    /**
     * @Route("/{author}", name="show_author", methods={"GET"})
     * @param string $author
     * @param Request $request
     * @return JsonResponse
     */
    public function show(string $author, Request $request)
    {
        $author = $this->normalizeAuthorName($author);
        $limit = $request->query->get('limit');
        if ($limit > 10) {
            return new JsonResponse('Quote limit is 10', 400);
        }

        $result = $this->repository->findByAuthor($author);
        $count = count($result);

        if ($count === 0) {
            return new JsonResponse($result, 204);
        }

        if ($limit >= 1 && $count > $limit) {
            $result = array_slice($result, 0, $limit);
        }
        $transformHelper = $this->transformHelper;
        foreach ($result as $key => $value) {
            $result[$key] = $transformHelper($value);
        }

        return new JsonResponse($result);
    }


    protected function normalizeAuthorName($string)
    {
        return ucwords(str_replace('-', ' ', $string));
    }

    protected function slugify($string)
    {
        return mb_strtolower(str_replace(' ', '-', $string));
    }
}
