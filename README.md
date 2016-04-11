composer/ca-bundle
==================

Small utility library that lets you find a path to the system CA bundle,
and includes a fallback to the Mozilla CA bundle.

Originally written as part of [composer/composer](https://github.com/composer/composer),
now extracted and made available as a stand-alone library.


Installation
------------

Install the latest version with:

```bash
$ composer require composer/ca-bundle
```


Requirements
------------

* PHP 5.3.2 is required but using the latest version of PHP is highly recommended.


Basic usage
-----------

# `Composer\CaBundle\CaBundle`

- `CaBundle::getSystemCaRootBundlePath()`: Returns the system CA bundle path, or a path to the bundled one as fallback
- `CaBundle::getBundledCaBundlePath()`: Returns the path to the bundled CA file
- `CaBundle::validateCaFile($filename)`: Validates a CA file using opensl_x509_parse only if it is safe to use
- `CaBundle::isOpensslParseSafe()`: Test if it is safe to use the PHP function openssl_x509_parse()
- `CaBundle::reset()`: Resets the static caches


License
-------

composer/ca-bundle is licensed under the MIT License, see the LICENSE file for details.
