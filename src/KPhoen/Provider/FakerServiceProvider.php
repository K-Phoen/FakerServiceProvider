<?php

namespace KPhoen\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A Faker service provider for Silex.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class FakerServiceProvider implements ServiceProviderInterface
{
    protected $factoryClass, $guessLocale;

    /**
     * Add a 'faker' service initialized with the language the most adapted to
     * the request.
     *
     * @param string    $factoryClass   The factory class used to create Faker's
     *                                  generator instance.
     * @param bool      $guessLocale    Indicates if the locale should be guessed
     *                                  from the request or read from the
     *                                  application settings.
     */
    public function __construct($factoryClass = '\Faker\Factory', $guessLocale = true)
    {
        $this->factoryClass = $factoryClass;
        $this->guessLocale = $guessLocale;
    }

    public function register(Application $app)
    {
        if (!$this->guessLocale) {
            $this->injectService($app);
        }
    }

    /**
     * Add a 'faker' service initialized with the language the most adapted to
     * the request.
     *
     * @param Application $app The current application.
     */
    public function boot(Application $app)
    {
        if ($this->guessLocale) {
            $this->injectService($app);
        }
    }

    protected function injectService(Application $app)
    {
        $app['faker'] = $app->share(function($app) {
            if ($this->guessLocale) {
                $language = $this->getBestLanguage($app['request'], $app['locale']);
            } else {
                $language = $app['locale'];
            }

            $factoryClass = $this->factoryClass;
            return $factoryClass::create($language);
        });
    }

    /**
     * Finds the best language for a given request.
     *
     * @param Request $request  The request.
     * @param string  $default  The default language if nothing satisfying is
     *                          found in the request.
     */
    protected function getBestLanguage(Request $request, $default = null)
    {
        foreach ($request->getLanguages() as $language) {
            if (strpos($language, '_') !== false) {
                return $language;
            }
        }

        return $default;
    }
}
