Symfony2 Bundle - API short and fast usual render

# Bundle dependency

This bundle require another bundles:

https://github.com/schmittjoh/JMSSerializerBundle
https://github.com/FriendsOfSymfony/FOSRestBundle

# Installation

## Composer

Write in terminal:
```
composer require sopinet/apihelper-bundle "1.0"
```

## AppKernel

Enable the Bundle, Add to Kernel:
```
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    // ...

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Sopinet\ApiHelperBundle\SopinetApiHelperBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
        );

        // ...
    }
}
```

## Add to config

Configure FOSRestAPI:

```
fos_rest:
    routing_loader:
        default_format: json
```

# Use

```
use SopinetApiHelperBundle\Services\ApiHelper;
$apiHelper = $this->get('sopinet_apihelperbundle_apihelper');
...
return $apiHelper->responseOk();
```

TODO: More Documentation
