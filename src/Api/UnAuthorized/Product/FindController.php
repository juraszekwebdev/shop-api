<?php


namespace App\Api\UnAuthorized\Product;


use App\Entity\Order;
use App\Entity\Product;
use App\JWT\JWTManager;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FindController extends AbstractController
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
     * @Route("/product/find", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function find(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);

        $find = null;
        try {
            $find = $this->getDoctrine()->getRepository(Order::class)->createQueryBuilder('p')
                ->setParameter('id', $requestedContent['id'])
                ->where('p.id = :id')
                ->getQuery()
                ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
        } catch (NoResultException | NonUniqueResultException $e) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'statusMsg' => $e->getMessage(),
                    'data' => null,
                ],
                JsonResponse::HTTP_OK
            );
        }

        return new JsonResponse(
            [
                'data' => $find,
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }
}