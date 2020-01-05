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
     * @var QuoteRepository
     */
    private $repository;

    public function __construct(QuoteRepository $repository)
    {
        $this->repository = $repository;
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
        foreach ($result['quotes'] as $key => $quote) {
            $author = $quote['author'];
            $data[$author]['quotes'][] = TransformHelper::shout($quote['quote']);
            $data[$author]['_links'] = $this->generateUrl(
                'show_author',
                ['author' => TransformHelper::slugify($author)],
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
        foreach ($result as $key => $value) {
            $result[$key] = TransformHelper::shout($value);
        }

        return new JsonResponse($result);
    }

}
