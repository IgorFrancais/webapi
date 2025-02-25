<?php

declare(strict_types=1);

namespace WebApi\GoogleGeoCodingApi\Model;

use Exception;
use Magento\Framework\HTTP\Client\CurlFactory;
use WebApi\GoogleGeoCodingApi\Api\Data\GoogleGeoCodingApiInterface;
use WebApi\GoogleGeoCodingApi\Helper\GoogleGeoCodingApiHandler;
use function sprintf;

class GoogleGeoCodingApi implements GoogleGeoCodingApiInterface
{
    /**
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \WebApi\GoogleGeoCodingApi\Helper\GoogleGeoCodingApiHandler $apiHandler
     */
    public function __construct(
        private readonly CurlFactory $curlFactory,
        private readonly GoogleGeoCodingApiHandler $apiHandler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $postalCode): string
    {
        if (!$this->isPostalCodeValid($postalCode)) {
            return sprintf(GoogleGeoCodingApiInterface::POSTAL_CODE_VALIDATION_ERROR, $postalCode);
        }

        $url = $this->apiHandler->prepareUrl($postalCode);

        $curl = $this->curlFactory->create();

        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->addHeader('cache-control', 'no-cache');

        try {
            $curl->get($url);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->apiHandler->getResponseMessage($curl->getBody());
    }

    /**
     * Validate postal code - should be Canadian only
     *
     * @param string $postalCode
     * @return bool
     */
    private function isPostalCodeValid(string $postalCode): bool
    {
        return (bool) preg_match(GoogleGeoCodingApiInterface::POSTAL_CODE_VALIDATION_CA, $postalCode);
    }
}
