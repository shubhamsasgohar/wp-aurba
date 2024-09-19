<?php

namespace GF_Maxpay\Dependencies\Component;

use GF_Maxpay\Dependencies\Model\IdentityInterface;
use GF_Maxpay\Dependencies\Util\CurlClient;
use GF_Maxpay\Dependencies\Util\SignatureHelper;
use GF_Maxpay\Dependencies\Util\Validator;
use Psr\Log\LoggerInterface;

/**
 * Class CancelPostTrialBuilder
 * @package Maxpay\Lib\Component
 */
class CancelPostTrialBuilder extends BaseBuilder
{
    /** @var IdentityInterface */
    private $identity;

    /** @var string */
    private $action = 'api/cancel_post_trial';

    /** @var string */
    private $transactionId;

    /** @var ClientInterface */
    private $client;

    /** @var ValidatorInterface */
    private $validator;

    /** @var string */
    private $baseHost;

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
        $this->transactionId = $this->validator->validateString('transactionId', $transactionId);
        $this->baseHost = $this->validator->validateString('baseHost', $baseHost);
        $this->signatureHelper = new SignatureHelper();

        $this->client = new CurlClient($baseHost . $this->action, $logger);
        $logger->info('Cancel post trial builder successfully initialized');
    }

    /**
     * @return array
     * @throws GeneralMaxpayException
     */
    public function send(): array
    {
        $data = [
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
