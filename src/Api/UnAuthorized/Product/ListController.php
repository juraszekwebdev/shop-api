<?php


namespace App\Api\UnAuthorized\Product;


use App\Entity\Product;
use App\JWT\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @var JWTManager $jwtManager
     */
    private $jwtManager;

    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/product/list", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);

        $list = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')->getQuery()->getArrayResult();

        return new JsonResponse(
            [
                'data' => $list,
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }
}