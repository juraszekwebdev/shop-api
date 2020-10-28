<?php


namespace App\JWT;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class JWTCreatedListener extends AbstractController
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $payload['email']
        ]);
        $payload['id'] = $user->getId();
        $payload['address'] = $user->getAddress();
        $payload['personalData'] = $user->getPersonalData();

        $exp = new \DateTime();
        $exp->modify('+2 hours');
        $payload['exp'] = $exp->getTimestamp();

        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}