<?php

use KPhoen\Provider\FakerServiceProvider;
use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class FakerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testBootCreatesServicesWithGuessEnabled()
    {
        $serviceProvider = new FakerServiceProvider();
        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getLanguages')
            ->will($this->returnValue(array('en_US')));

        $application = new Application();
        $application['locale'] = 'fr_FR';
        $application['request'] = $request;

        $serviceProvider->boot($application);

        $this->assertInstanceOf('\Faker\Generator', $application['faker']);
        $this->checkProvidersLocale($application['faker']->getProviders(), 'en_US');
    }

    public function testBootCreatesServicesWithGuessDisabled()
    {
        $serviceProvider = new FakerServiceProvider($factoryClass = '\Faker\Factory', $guessLocale = false);
        $application = new Application();
        $application['locale'] = 'fr_FR';

        $serviceProvider->boot($application);

        $this->assertInstanceOf('\Faker\Generator', $application['faker']);
        $this->checkProvidersLocale($application['faker']->getProviders(), 'fr_FR');
    }

    protected function checkProvidersLocale(array $providers, $expectedLocale)
    {
        foreach ($providers as $provider) {
            $classData = explode('\\', get_class($provider));

            // locale independant provider
            if (count($classData) !== 4) {
                continue;
            }

            $this->assertEquals($expectedLocale, $classData[2]);
        }
    }
}
