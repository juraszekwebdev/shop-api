<?php


namespace App\Api\Authorized\User;


use App\Entity\User;
use App\JWT\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
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
     * @Route("/changeUserPassword", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request)
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

        $object = $this->getDoctrine()->getRepository(User::class)->find($decodedToken['id']);
        $object->setPassword(password_hash($requestedContent['newPassword'], PASSWORD_BCRYPT));
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();

        return new JsonResponse(
            [
                'data' => null,
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }

    private function validate($payload, $decodedToken)
    {
        if(!$this->isOldPasswordValid($decodedToken['id'], $payload['oldPassword'])) {
            return [
                'data' => null,
                'status' => 'error',
                'statusMsg' => 'Old password is incorrect',
            ];
        }

        if($payload['newPassword'] !== $payload['repeatNewPassword']) {
            return [
                'data' => null,
                'status' => 'error',
                'statusMsg' => 'New passwords must be match',
            ];
        }

        return [
            'data' => null,
            'status' => 'ok',
            'statusMsg' => '',
        ];
    }


    private function isOldPasswordValid($id, $oldPassword)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        return password_verify($oldPassword, $user->getPassword());
    }
}