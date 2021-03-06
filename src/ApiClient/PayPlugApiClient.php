<?php

declare(strict_types=1);

namespace PayPlug\SyliusPayPlugPlugin\ApiClient;

use Payplug\Core\HttpClient;
use Payplug\Notification;
use Payplug\Payplug;
use Payplug\Resource\IVerifiableAPIResource;
use Payplug\Resource\Payment;
use Payplug\Resource\Refund;
use PayPlug\SyliusPayPlugPlugin\PayPlugSyliusPayPlugPlugin;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use Webmozart\Assert\Assert;

class PayPlugApiClient implements PayPlugApiClientInterface
{
    public function initialise(string $secretKey): void
    {
        Payplug::setSecretKey($secretKey);
        HttpClient::addDefaultUserAgentProduct(
            'PayPlug-Sylius',
            PayPlugSyliusPayPlugPlugin::VERSION,
            'Sylius/' . Kernel::VERSION
        );
    }

    public function createPayment(array $data): Payment
    {
        $payment = \Payplug\Payment::create($data);
        Assert::isInstanceOf($payment, Payment::class);

        return $payment;
    }

    public function refundPayment(string $paymentId): Refund
    {
        /** @var Refund|null $refund */
        $refund = \Payplug\Refund::create($paymentId);
        Assert::isInstanceOf($refund, Refund::class);

        return $refund;
    }

    public function refundPaymentWithAmount(string $paymentId, int $amount, int $refundId): Refund
    {
        /** @var Refund|null $refund */
        $refund = \Payplug\Refund::create($paymentId, [
            'amount' => $amount,
            'metadata' => ['refund_from_sylius' => true],
        ]);
        Assert::isInstanceOf($refund, Refund::class);

        return $refund;
    }

    public function treat(string $input): IVerifiableAPIResource
    {
        return Notification::treat($input);
    }

    public function retrieve(string $paymentId): Payment
    {
        $payment = \Payplug\Payment::retrieve($paymentId);
        Assert::isInstanceOf($payment, Payment::class);

        return $payment;
    }
}
