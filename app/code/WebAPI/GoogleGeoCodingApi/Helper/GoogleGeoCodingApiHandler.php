<?php

declare(strict_types=1);

namespace WebApi\GoogleGeoCodingApi\Helper;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use WebApi\GoogleGeoCodingApi\Api\Data\GoogleGeoCodingApiInterface;

class GoogleGeoCodingApiHandler
{
    /**
     * @param \Magento\Framework\Serialize\SerializerInterface $json
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     */
    public function __construct(
        private readonly SerializerInterface $json,
        private readonly UrlInterface $urlBuilder,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly DeploymentConfig $deploymentConfig
    ) {
    }

    /**
     * Prepare url for GoogleGeoCodingApi call
     *
     * @param string $postalCode
     * @return string
     */
    public function prepareUrl(string $postalCode): string
    {
        $queryParams = [
            'address' => $postalCode,
            'key' => $this->getGeocodingApiKey()
        ];

        $urlToCall = $this->urlBuilder->getDirectUrl(
            $this->getGeocodingUrl(),
            ['_query' => $queryParams]
        );

        $baseUrl = $this->urlBuilder->getBaseUrl();

        return GoogleGeoCodingApiInterface::URL_PREFIX . str_replace($baseUrl, '', $urlToCall);
    }

    /**
     * Prepare response message - successfull or error
     *
     * @param string $response
     * @return string
     */
    public function getResponseMessage(string $response): string
    {
        if ($response === '') {
            return GoogleGeoCodingApiInterface::REQUEST_MSG_EMPTY;
        }

        try {
            $arrayResponse = $this->json->unserialize($response);
        } catch (Exception) {
            return GoogleGeoCodingApiInterface::REQUEST_MSG_ERROR;
        }

        if ($arrayResponse['status'] !== GoogleGeoCodingApiInterface::GOOGLE_GEOCODING_RESULT_STATUS_OK) {
            return array_key_exists('error_message', $arrayResponse)
                ? $arrayResponse['error_message']
                : GoogleGeoCodingApiInterface::REQUEST_MSG_ERROR;
        }

        return $this->formatResponse($arrayResponse);
    }

    /**
     * Format response
     *
     * @param array $response
     * @return string
     */
    private function formatResponse(array $response): string
    {
        $addressComponents = $response['results'][0]['address_components'];

        $city = '';
        $province = '';

        foreach ($addressComponents as $addressComponent) {
            if ($addressComponent['types'][0] === 'locality') {
                $city = $addressComponent['long_name'];
            }

            if ($addressComponent['types'][0] == 'administrative_area_level_1') {
                $province = $addressComponent['short_name'];
            }
        }

        $formattedAddress = [
            'formatted_address' => $response['results'][0]['formatted_address'],
            'city' => $city,
            'province' => $province,
        ];

        return  $this->json->serialize($formattedAddress);
    }

    /**
     * Get ApiURL for GoogleGeocoding
     *
     * @return string
     */
    private function getGeocodingUrl(): string
    {
        return $this->scopeConfig->getValue(GoogleGeoCodingApiInterface::GOOGLE_GEOCODING_APIURL_XML);
    }

    /**
     * Get ApiURL for GoogleGeocoding
     *
     * @return string
     */
    private function getGeocodingApiKey(): string
    {
        return $this->deploymentConfig->get(GoogleGeoCodingApiInterface::GOOGLE_GEOCODING_APIKEY_XML) ?? '';
    }
}
