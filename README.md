# IntegratedBehatExtensions #
This bundle enhances FeatureContext classes with traits for Behat with Mink  

## Requirements ##
* See the require section in the composer.json

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ composer require integrated/integrated-behat-extensions:master
    
### Configuration ###
The extensions require a autoload path to be defined in the configuration of Behat.

    // behat.yml
    default:
        ...
        autoload:
            "" : %paths.base%/features/bootstrap
            "Integrated\\Behat" : %paths.base%/vendor/integrated/behat-extensions/src
    ...
    
In order to be able to catch mails send from the Symfony application the spool of Swiftmailer must be written to a directory.
    
    // app/config/config_test.yml
    ...
    swiftmailer:
        disable_delivery: true
        spool:
            type: file
            path: %kernel.cache_dir%/spool

## Using Extensions ##
All extensions that enhance your FeatureContext class are in the Extension folder of the project.
The other classes are used in an supporting way of the extensions. 
Depending on the extension type abstract methods are be defined in the trait.
There mostly are declared within the MinkContext.

### Example ###
The traits are placed within the FeatureContext class.  

    // features/bootstrap/FeatureContext.php
    ...
    use Integrated/Behat/Extension/Login/SymfonyLogin;
    ...
    class FeatureContext extends MinkContext implements Context    
    {
        use SymfonyLogin; 
    ...    


## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
