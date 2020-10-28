<?php


namespace App\Auth;


use App\Entity\User;
use App\JWT\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
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
     * @Route("/register", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);

        $validate = $this->validate($requestedContent);
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

        $object = new User();
        $object->setEmail($requestedContent['email']);
        $object->setPassword(password_hash($requestedContent['password'], PASSWORD_BCRYPT));
        $object->setAddress(null);
        $object->setPersonalData($requestedContent['personalData']);
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

    private function validate($payload)
    {
        if($this->isExists($payload['email'])) {
            return [
                'data' => null,
                'status' => 'error',
                'statusMsg' => 'User with this email already exists',
            ];
        }

        if($payload['password'] !== $payload['repeatPassword']) {
            return [
                'data' => null,
                'status' => 'error',
                'statusMsg' => 'Passwords must be match',
            ];
        }

        return [
            'data' => null,
            'status' => 'ok',
            'statusMsg' => '',
        ];
    }


    private function isExists($email)
    {
        return $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}