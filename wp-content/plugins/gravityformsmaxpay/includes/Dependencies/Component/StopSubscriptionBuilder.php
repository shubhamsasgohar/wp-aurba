<?php

namespace GF_Maxpay\Dependencies\Component;

use GF_Maxpay\Dependencies\Model\IdentityInterface;
use GF_Maxpay\Dependencies\Util\CurlClient;
use GF_Maxpay\Dependencies\Util\SignatureHelper;
use GF_Maxpay\Dependencies\Util\Validator;
use Psr\Log\LoggerInterface;

/**
 * Class StopSubscriptionBuilder
 * @package Maxpay\Lib\Component
 */
class StopSubscriptionBuilder extends BaseBuilder
{
    /** @var string */
    private $action = 'api/cancel';

    /** @var IdentityInterface */
    private $identity;

    /** @var ValidatorInterface */
    private $validator;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $baseHost;

    /** @var string */
    private $userId;

    /** @var string */
    private $transactionId;

    /** @var ClientInterface */
    private $client;

    /** @var SignatureHelper */
    private $signatureHelper;

    /**
     * @param IdentityInterface $identity
     * @param string $userId
     * @param string $transactionId
     * @param LoggerInterface $logger
     * @param string $baseHost
     */
    public function __construct(
        IdentityInterface $identity,
        string $userId,
        string $transactionId,
        LoggerInterface $logger,
        string $baseHost
    ) {
        parent::__construct($logger);

        $this->validator = new Validator();
        $this->identity = $identity;
        $this->logger = $logger;
        $this->userId = $this->validator->validateString('userId', $userId);
        $this->transactionId = $this->validator->validateString('transactionId', $transactionId);
        $this->baseHost = $baseHost;
        $this->client = new CurlClient($this->baseHost . $this->action, $logger);
        $this->signatureHelper = new SignatureHelper();

        $this->logger->info('Stop subscription builder successfully initialized');
    }

    /**
     * @return array
     * @throws GeneralMaxpayException
     */
    public function send(): array
    {
        $data = [
            'uniqueUserId' => $this->userId,
            'transactionId' => $this->transactionId,
            'publicKey' => $this->identity->getPublicKey()
        ];

        $data['signature'] = $this->signatureHelper->generateForArray(
            $data,
            $this->identity->getPrivateKey(),
            true
        );

        return $this->prepareAnswer($this->client->send($data));
    }
}
