<?php

declare(strict_types=1);

namespace WebApi\GoogleGeoCodingApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface GoogleGeoCodingApiInterface extends ExtensibleDataInterface
{
    public const string GOOGLE_GEOCODING_APIURL_XML = 'googlegeocoding/geocodingconfig/apiurl';
    public const string GOOGLE_GEOCODING_APIKEY_XML = 'system/googlegeocoding/geocodingconfig/apikey';
    public const string GOOGLE_GEOCODING_RESULT_STATUS_OK = 'OK';

    public const string POSTAL_CODE_VALIDATION_CA = '/^([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d)$/';
    public const string POSTAL_CODE_VALIDATION_ERROR = 'Postal code %s is not valid';
    public const string URL_PREFIX = 'https://';

    public const REQUEST_MSG_ERROR = 'Error during request';
    public const REQUEST_MSG_EMPTY = 'Request information is empty';

    /**
     * Get data by PostCode
     *
     * @param string $postalCode
     *
     * @return string
     */
    public function get(string $postalCode): string;
}
