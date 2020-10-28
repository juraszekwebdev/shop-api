<?php


namespace App\Api\Authorized\Order;


use App\Entity\Order;
use App\Entity\Product;
use App\JWT\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddController extends AbstractController
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
     * @Route("/order/add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);
        $decodedToken = $this->jwtManager->decodeToken($request);

        $validate = $this->validate($requestedContent, $decodedToken);
        if($validate['status'] === 'error') {
            return new JsonResponse(
                [
                    'data' => null,
                    'status' => $validate['status'],
                    'statusMsg' => $validate['statusMsg'],
                ],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $object = new Order();
        $object->setOwner($decodedToken['id']);
        $object->setData($requestedContent['orderData']);
        $object->setOrderId($requestedContent['orderData']['orderId']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();

        return new JsonResponse(
            [
                'data' => $object->getId(),
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }

    private function validate($payload, $decodedToken)
    {
        return [
            'data' => null,
            'status' => 'ok',
            'statusMsg' => '',
        ];
    }
}