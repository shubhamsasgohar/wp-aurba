<?php

namespace Maxpay\Lib\Maxpay;

use Maxpay\Lib\Component\ButtonBuilder;
use Maxpay\Lib\Component\CancelPostTrialBuilder;
use Maxpay\Lib\Component\RebillBuilder;
use Maxpay\Lib\Component\RefundBuilder;
use Maxpay\Lib\Component\StopSubscriptionBuilder;
use Maxpay\Lib\Exception\GeneralMaxpayException;
use Maxpay\Lib\Model\Identity;
use Maxpay\Lib\Model\IdentityInterface;
use Maxpay\Lib\Util\SignatureHelper;
use Maxpay\Lib\Psr\Log\LoggerInterface;
use Maxpay\Lib\Psr\Log\NullLogger;

/**
 * Class Scriney
 * @package Maxpay
 */
class Scriney implements ScrineyInterface
{
    const VALIDATION_TYPE_API = "API";
    const VALIDATION_TYPE_CALLBACK = "CALLBACK";

    /** @var string */
    private $hostBase;

    /** @var LoggerInterface */
    private $logger;

    /** @var IdentityInterface */
    private $identity;

    /**
     * @param string $publicKey Available in your Mportal
     * @param string $privateKey Available in your Mportal
     * @param LoggerInterface|null $logger Any PSR-3 logger
     * @param string $hostBase
     * @throws GeneralMaxpayException
     */
    public function __construct(
        $publicKey,
        $privateKey,
        LoggerInterface $logger = null,
        $hostBase = 'https://hpp.maxpay.com/'
    ) {
        $this->logger = is_null($logger) ? new NullLogger() : $logger;
        $this->hostBase = $hostBase;

        try {
            $this->identity = new Identity($publicKey, $privateKey);
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                'Wrong init param',
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }

        $this->logger->info(
            'Scriney object successfully built',
            []
        );
    }

    /**
     * @param string $billToken
     * @param string $userId
     * @throws GeneralMaxpayException
     * @return RebillBuilder
     */
    public function createRebillRequest($billToken, $userId)
    {
        try {
            return new RebillBuilder($this->identity, $billToken, $userId, $this->logger, $this->hostBase);
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                "Can't init rebill builder",
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Rebill builder initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }
    }

    /**
     * Method build integration code of pay button
     *
     * @param string $userId User Id in your system
     * @throws GeneralMaxpayException
     * @return ButtonBuilder
     */
    public function buildButton($userId)
    {
        try {
            return new ButtonBuilder($this->identity, $userId, $this->logger, $this->hostBase);
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                "Can't init button builder",
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Page builder initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }
    }

    /**
     * Method stop subscription
     *
     * @param string $transactionId
     * @param string $userId
     * @throws GeneralMaxpayException
     * @return mixed[]
     */
    public function stopSubscription($transactionId, $userId)
    {
        try {
            $subscriptionBuilder =  new StopSubscriptionBuilder(
                $this->identity,
                $userId,
                $transactionId,
                $this->logger,
                $this->hostBase
            );

            return $subscriptionBuilder->send();
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                "Can't init stop subscription builder",
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Stop subscription builder initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }
    }

    /**
     * Method refunds transaction
     *
     * @param string $transactionId
     * @throws GeneralMaxpayException
     * @return mixed[]
     */
    public function refund($transactionId)
    {
        try {
            $refundBuilder =  new RefundBuilder(
                $this->identity,
                $transactionId,
                $this->logger,
                $this->hostBase
            );

            return $refundBuilder->send();
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                "Can't init refund builder",
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Refund builder initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }
    }

    /**
     * Method for validate api result
     *
     * @param array $data result received from Maxpay API
     * @throws GeneralMaxpayException
     * @return bool
     */
    public function validateApiResult(array $data)
    {
        return $this->validate(self::VALIDATION_TYPE_API, $data);
    }

    /**
     * Method for validate callback
     *
     * @param array $data callback data from Maxpay
     * @throws GeneralMaxpayException
     * @return bool
     */
    public function validateCallback(array $data)
    {
        return $this->validate(self::VALIDATION_TYPE_CALLBACK, $data);
    }

    /**
     * @param string $validationType
     * @param mixed[] $data
     * @throws GeneralMaxpayException
     * @return bool
     */
    private function validate($validationType, $data)
    {
        switch ($validationType) {
            case self::VALIDATION_TYPE_CALLBACK:
            case self::VALIDATION_TYPE_API:
                try {
                    $signatureHelper = new SignatureHelper();
                    $checkSum = null;
                    $callbackData = [];
                    foreach ($data as $k => $v) {
                        if ($k !== 'checkSum') {
                            $callbackData[$k] = $v;
                        } else {
                            $checkSum = $v;
                        }
                    }

                    if (is_null($checkSum)) {
                        $this->logger->error(
                            'checkSum field is required',
                            []
                        );
                        return false;
                    }

                    if ($checkSum !== $signatureHelper->generate($callbackData, $this->identity->getPrivateKey())) {
                        $this->logger->error(
                            'Checksum validation failure',
                            []
                        );
                        return false;
                    }

                    $this->logger->info(
                        'Checksum is valid',
                        []
                    );
                    return true;
                } catch (\Exception $ex) {
                    $this->logger->error(
                        'Checksum validation failure',
                        [
                            'exception' => $ex,
                        ]
                    );

                    throw new GeneralMaxpayException($ex->getMessage(), $ex);
                }

                break;
            default:
                $this->logger->error(
                    'Invalid validation type received',
                    [
                        'incomingType' => $validationType
                    ]
                );

                throw new GeneralMaxpayException('Invalid validation type received');
        }
    }

    /**
     * Method cancel post trial
     *
     * @param string $transactionId
     * @throws GeneralMaxpayException
     * @return mixed[]
     */
    public function cancelPostTrial($transactionId)
    {
        try {
            $builder =  new CancelPostTrialBuilder(
                $this->identity,
                $transactionId,
                $this->logger,
                $this->hostBase
            );

            return $builder->send();
        } catch (GeneralMaxpayException $e) {
            $this->logger->error(
                "Can't init cancel post trial builder",
                [
                    'exception' => $e,
                ]
            );

            throw $e;
        } catch (\Exception $ex) {
            $this->logger->error(
                'Cancel post trial builder initialization failed',
                [
                    'exception' => $ex,
                ]
            );

            throw new GeneralMaxpayException($ex->getMessage(), $ex);
        }
    }
}
