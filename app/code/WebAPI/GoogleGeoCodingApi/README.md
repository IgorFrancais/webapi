### Authors
* Igor Povkh (iregards@gmail.com)

### Changelog
* 1.0.0
  * URL to call Api: 
   ```shell
      https://magento.test/rest/V1/googleapi/?postal_code=xxxxxx
  ```
  
  * *GoogleGeocodding* **url** is stored in ```googlegeocoding/geocodingconfig/apiurl``` (defined in ```config.xml```)
  * *GoogleGeocodding* **apiKey** is stored in ```env.php```:
    ```php
    'system' => [
        'googlegeocoding' => [
            'geocodingconfig' => [
                'apikey' => 'some_api_key_value'
            ]
        ]
    ]
    ```
  * Web Api Input Limits is activated (defined in ```config.xml```)
