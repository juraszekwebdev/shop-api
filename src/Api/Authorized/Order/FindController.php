<?php


namespace App\Api\Authorized\Order;


use App\Entity\Order;
use App\JWT\JWTManager;
use Doctrine\ORM\AbstractQuery;
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
     * @Route("/order/find", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function find(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);

        $find = $this->getDoctrine()->getRepository(Order::class)->createQueryBuilder('o')
            ->setParameter('id', $requestedContent['id'])
            ->where('o.id = :id')
            ->getQuery()
            ->getSingleResult(AbstractQuery::HYDRATE_ARRAY);


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