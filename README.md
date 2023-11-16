Symfony2 Bundle - API short and fast usual render


# Installation

## Composer

Write in terminal:
```
composer require sopinet/apihelper-bundle-2 "1.0"
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

# Bundle dependency

Remember, this bundle has another bundles dependency:

https://github.com/schmittjoh/JMSSerializerBundle

https://github.com/FriendsOfSymfony/FOSRestBundle

Configuration about these bundles was included.
If you have any problem with configuration, please, review official documentation about these bundles.


# Use

```
use SopinetApiHelperBundle\Services\ApiHelper;
$apiHelper = $this->get('sopinet_apihelperbundle_apihelper');
...
return $apiHelper->responseOk();
```

TODO: More Documentation
