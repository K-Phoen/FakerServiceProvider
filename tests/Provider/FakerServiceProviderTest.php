<?php

use KPhoen\Provider\FakerServiceProvider;
use Silex\Application;
use Symfony\Component\HttpKernel\KernelEvents;

class FakerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    Identifier "faker" is not defined.
     */
    public function testRegisterDoesNotCreatesServicesWithGuessEnabled()
    {
        $serviceProvider = new FakerServiceProvider($factoryClass = '\Faker\Factory', $guessLocale = true);
        $application = new Application();

        $serviceProvider->register($application);
        $application['faker'];
    }

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

    public function testRegisterCreatesServicesWithGuessDisabled()
    {
        $serviceProvider = new FakerServiceProvider($factoryClass = '\Faker\Factory', $guessLocale = false);
        $application = new Application();
        $application['locale'] = 'fr_FR';

        $serviceProvider->register($application);

        $this->assertInstanceOf('\Faker\Generator', $application['faker']);
        $this->checkProvidersLocale($application['faker']->getProviders(), 'fr_FR');
    }

    /**
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    Identifier "faker" is not defined.
     */
    public function testBootDoesNotCreatesServicesWithGuessDisabled()
    {
        $serviceProvider = new FakerServiceProvider($factoryClass = '\Faker\Factory', $guessLocale = false);
        $application = new Application();

        $serviceProvider->boot($application);
        $application['faker'];
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
