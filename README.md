Symfony2 Bundle - API short and fast usual render

# Installation

Write in terminal:
```
composer require sopinet/apihelper-bundle "1.0"
```

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
        );

        // ...
    }
}
```

# Use

```
use SopinetApiHelperBundle\Services\ApiHelper;
$apiHelper = $this->get('sopinet_apihelperbundle_apihelper');
...
return $apiHelper->responseOk();
```

TODO: More Documentation
