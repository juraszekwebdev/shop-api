<?php


namespace App\JWT;


use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;

class JWTManager
{
    /**
     * @var TokenExtractorInterface
     */
    private $tokenExtractor;
    /**
     * @var JWTEncoderInterface
     */
    private $tokenManager;

    /**
     * JWTManager constructor.
     * @param TokenExtractorInterface $tokenExtractor
     * @param JWTEncoderInterface $tokenManager
     */
    public function __construct(TokenExtractorInterface $tokenExtractor, JWTEncoderInterface $tokenManager)
    {
        $this->tokenExtractor = $tokenExtractor;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param $request
     * @return array|\Exception|JWTDecodeFailureException
     */
    public function decodeToken($request)
    {
        $token = $this->tokenExtractor->extract($request);
        try {
            return $this->tokenManager->decode($token);
        } catch (JWTDecodeFailureException $e) {
            return $e;
        }

    }
}