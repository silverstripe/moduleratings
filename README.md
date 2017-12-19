# SilverStripe module ratings

This module provides a check suite, and a set of automated checks that can be run over a SilverStripe module
to determine a "quality rating".

This library can be installed into a SilverStripe 3 or 4 project that uses Composer.

## Installation

You can install this library with Composer if you want to use its public API:

```
composer require silverstripe/moduleratings
```

You can also install the [silverstripe/moduleratings-plugin](https://github.com/creative-commoners/moduleratings-plugin)
Composer plugin, which will provide a Composer command you can run locally to check module quality ratings.
Please see the readme in that module for more information on using it.

## Requirements

* symfony/yaml 3 or 4

**Note:** If you have conflicts with either symfony/yaml or symfony/console during installation, you may
need to manually require an older version of one or both of those packages, e.g.:

``` 
composer require symfony/yaml ~3.0
composer require symfony/console ~3.0
```

## Using the API

To create a check suite, use the `SilverStripe\ModuleRatings\CheckSuite` class. You will need to provide the
directory path to the module code you want to check, and optionally the GitHub repository slug for the
module (note: only GitHub supported at this stage). Providing the repository slug will enable checks that
look at external continuous integration system APIs to determine quality metrics (e.g. Travis, Scrutinizer).

```php
$checkSuite = new \SilverStripe\ModuleRatings\CheckSuite();

$checkSuite
    ->setModuleRoot('/path/to/silverstripe/framework')
    ->setRepositorySlug('silverstripe/silverstripe-framework');

$checkSuite->run();

echo 'Framework has scored ' . $checkSuite->getScore() . ' out of 100 points. Details:', PHP_EOL;
print_r($checkSuite->getCheckDetails());
```

The return data from `CheckSuite::getCheckDetails` is an array with the following example structure
(note that example is JSON encoded):

```json
{
    "good_code_coverage": {
        "description": "Has a \"good\" level of code coverage (greater than 40%, requires slug)",
        "points": 5,
        "maximum": 5
    },
    "has_code_of_conduct_file": {
        "description": "Has a code of conduct file",
        "points": 2,
        "maximum": 2
    },
    "coding_standards": {
        "description": "The PHP code in this module passes the SilverStripe lint rules (mostly PSR-2)",
        "points": 0,
        "maximum": 10
    }
 }
```

## Available checks

@todo: list all checks with maximum points and description

## Thanks!

A huge thank you to [Chris Pitt](https://github.com/assertchris) who originally wrote Helpful Robot, the
inspiration for this library.

The checks in this library are heavily inspired by the original Helpful Robot checks, and are designed to
match the SilverStripe commercially supported module standard.

Without Helpful Robot the SilverStripe community would look a lot less tidy
today!
