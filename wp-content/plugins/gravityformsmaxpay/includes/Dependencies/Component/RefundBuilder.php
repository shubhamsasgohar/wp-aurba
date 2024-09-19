<?php

namespace GF_Maxpay\Dependencies\Component;

use GF_Maxpay\Dependencies\Model\IdentityInterface;
use GF_Maxpay\Dependencies\Util\CurlClient;
use GF_Maxpay\Dependencies\Util\SignatureHelper;
use GF_Maxpay\Dependencies\Util\Validator;
use Psr\Log\LoggerInterface;

/**
 * Class RefundBuilder
 * @package Maxpay\Lib\Component
 */
class RefundBuilder extends BaseBuilder
{
    /** @var string */
    private $action = 'api/extended_refund';

    /** @var IdentityInterface */
    private $identity;

    /** @var ValidatorInterface */
    private $validator;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $baseHost;

    /** @var string */
    private $transactionId;

    /** @var ClientInterface */
    private $client;

    /** @var SignatureHelper */
    private $signatureHelper;

    /**
     * @param IdentityInterface $identity
     * @param string $transactionId
     * @param LoggerInterface $logger
     * @param string $baseHost
     * @throws GeneralMaxpayException
     */
    public function __construct(
        IdentityInterface $identity,
        string $transactionId,
        LoggerInterface $logger,
        string $baseHost
    ) {
        parent::__construct($logger);

        $this->validator = new Validator();
        $this->identity = $identity;
        $this->logger = $logger;
        $this->transactionId = $this->validator->validateString('transactionId', $transactionId);
        $this->baseHost = $baseHost;
        $this->client = new CurlClient($this->baseHost . $this->action, $logger);
        $this->signatureHelper = new SignatureHelper();

        $this->logger->info('Refund builder successfully initialized');
    }

    /**
     * @param float $amount
     * @param string $currencyCode
     * @return array
     * @throws GeneralMaxpayException
     */
    public function send(float $amount, string $currencyCode): array
    {
        $data = [
            'transactionId' => $this->transactionId,
            'publicKey' => $this->identity->getPublicKey(),
            'amount' => $amount,
            'currency' => $currencyCode,
        ];

        $data['signature'] = $this->signatureHelper->generateForArray(
            $data,
            $this->identity->getPrivateKey(),
            true
        );

        return $this->prepareAnswer($this->client->send($data));
    }
}
