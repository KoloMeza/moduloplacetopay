<?php

namespace PlacetoPay\Payments\Helper;

use Magento\Framework\App\Config\Initial;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\LayoutFactory;
use Magento\Payment\Helper\Data as BaseData;
use Magento\Payment\Model\Config;
use Magento\Payment\Model\Method\Factory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use PlacetoPay\Payments\Constants\Country;
use PlacetoPay\Payments\Countries\CountryConfigInterface;
use PlacetoPay\Payments\Logger\Logger;
use PlacetoPay\Payments\Model\Adminhtml\Source\Mode;
use PlacetoPay\Payments\Model\Info as InfoFactory;

class Data extends BaseData
{
    public const CODE = 'placetopay';
    public const EXPIRATION_TIME_MINUTES_DEFAULT = 120;
    public const EXPIRATION_TIME_MINUTES_MIN = 10;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string|mixed
     */
    protected $mode;

    /**
     * @param InfoFactory
     */
    protected $infoFactory;

    public function __construct(
        Logger        $logger,
        Context       $context,
        LayoutFactory $layoutFactory,
        Factory       $paymentMethodFactory,
        Emulation     $appEmulation,
        Config        $paymentConfig,
        Initial       $initialConfig,
        InfoFactory   $infoFactory
    ) {
        parent::__construct(
            $context,
            $layoutFactory,
            $paymentMethodFactory,
            $appEmulation,
            $paymentConfig,
            $initialConfig
        );
        $this->infoFactory = $infoFactory;
        $this->logger = $logger;
        $this->version = '1.11.0';

        $this->mode = $this->scopeConfig->getValue(
            'payment/placetopay/placetopay_mode',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/merchant_id',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getLegalName(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/legal_name',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/email',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/phone',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getExpirationTime(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/expiration',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getFinalPage(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/final_page',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAllowPendingPayment(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/allow_pending_payment',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAllowPartialPayment(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/allow_partial_payment',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getHasCifin(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/has_cifin',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFillTaxInformation(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/fill_tax_information',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFillBuyerInformation(): bool
    {
        return !$this->scopeConfig->getValue(
            'payment/placetopay/fill_buyer_information',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSkipResult(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/skip_result',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getMinimumAmount(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/minimum_amount',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getMaximumAmount(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/maximum_amount',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getTaxRateParsing(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/tax_rate_parsing',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function getEmailSuccessOption(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/email_success',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getDiscount(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/discount',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getInvoice(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/invoice',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getActive(): bool
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/active',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/title',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * @return string|null
     */
    public function getCustomConnectionUrl(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/placetopay_custom_url',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isCustomEnvironment(): bool
    {
        return $this->getMode() === Mode::CUSTOM;
    }

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        $uri = null;
        $endpoints = $this->getEndpointsTo($this->getCountryCode());

        if ($this->isCustomEnvironment()) {
            return $this->getCustomConnectionUrl();
        }

        if (!empty($endpoints[$this->getMode()])) {
            return $endpoints[$this->getMode()];
        }

        return $uri;
    }

    /**
     * @return string|null
     */
    public function getTranKey(): ?string
    {
        switch ($this->mode) {
            case Mode::DEVELOPMENT:
                $tranKey = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_development_tk',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            case Mode::TEST:
                $tranKey = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_test_tk',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            case Mode::CUSTOM:
                $tranKey = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_custom_tk',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            default:
                $tranKey = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_production_tk',
                    ScopeInterface::SCOPE_STORE
                );
                break;
        }

        return $tranKey;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        switch ($this->mode) {
            case Mode::DEVELOPMENT:
                $login = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_development_lg',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            case Mode::TEST:
                $login = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_test_lg',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            case Mode::CUSTOM:
                $login = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_custom_lg',
                    ScopeInterface::SCOPE_STORE
                );
                break;
            default:
                $login = $this->scopeConfig->getValue(
                    'payment/placetopay/placetopay_production_lg',
                    ScopeInterface::SCOPE_STORE
                );
                break;
        }

        return $login;
    }

    public function getCountryCode(): ?string
    {
        return $this->scopeConfig->getValue(
            'general/country/default',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->scopeConfig->getValue(
            'payment/placetopay/payment_button_image',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $countryCode
     * @return string[]
     */
    public function getEndpointsTo($countryCode): array
    {
        /** @var CountryConfigInterface $config */
        foreach (Country::COUNTRIES_CONFIG as $config) {
            if (!$config::resolve($countryCode)) {
                continue;
            }

            return $config::getEndpoints($this->getTitle());
        }

        return [];
    }

    public function cleanText($text)
    {
        return preg_replace('/[(),.#!\-]/', '', $text);
    }

    public function getExpirationTimeMinutes()
    {
        $minutes = $this->getExpirationTime();

        return !is_numeric($minutes) || $minutes < self::EXPIRATION_TIME_MINUTES_MIN
            ? self::EXPIRATION_TIME_MINUTES_DEFAULT
            : $minutes;
    }

    /**
     * @return InfoFactory
     */
    public function getInfoModel(): InfoFactory
    {
        return $this->infoFactory;
    }

    public function getHeaders(): array
    {
        $objectManager = ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();

        $domain = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

        return [
            'User-Agent' => "magento2-module-payments/{$this->version} (origin:$domain; vr:" . $version . ')',
            'X-Source-Platform' => 'magento',
        ];
    }
}
