<?php


namespace App\Api\Authorized\Order;


use App\Entity\Order;
use App\JWT\JWTManager;
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
     * @Route("/order/list", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);
        $decodedToken = $this->jwtManager->decodeToken($request);

        $list = $this->getDoctrine()->getRepository(Order::class)->createQueryBuilder('o')
            ->getQuery()
            ->getArrayResult();

        $result = [];

        if($list) {
            foreach ($list as $item) {
                if($item['owner'] === $decodedToken['id']) {
                    $result[] = [
                        'id' => $item['id'],
                        'data' => $item['data']
                    ];
                }
            }
        }

        return new JsonResponse(
            [
                'data' => $result,
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }
}