<?php
/**
 * This is the first application file that gets loaded by the index.php file.
 *
 * @package JNT
 */
namespace HRQLS;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Monolog\Logger;
use JsonSchema\Uri\UriRetriever as UriRetriever;

/**
 * The class that manages startup of the application.
 *
 * All setup and preparation to serve pages start here.
 */
class Bootstrap
{
    /**
     * The postfix for the log tag.
     *
     * @var string
     */
    const LOGNAME = 'bootstrap';

    /** 
     * Application Log Name.
     *
     *    
     */
    const APPLOGNAME = 'hack';

    /**
     * The main Silex\Application instance used by the site.
     *
     * @var Silex\Application
     */
    private $app;

    /**
     * The application system config settings.
     *
     * @var array
     */
    private $config;

    /**
     * The application log provider.
     *
     * @var JNT\Helpers\LogProvider
     */
    private $logprovider;

    /**
     * Logger available for use by the bootstrap class.
     *
     * @var Monolog\Logger
     */
    private $logger;

    /**
     * The primary bootstrap for the application.
     *
     * Using dependency injection, this constructor takes a Silex\Application instance
     * which is used to spin-up the entire site.
     *
     * @param Silex\Application $app The main Silex instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Sets up all the routes to be used by the application.
     *
     * Any additional routes that need to be defined should be placed here.
     *
     * @return void
     */
    public function registerRoutes()
    {
        foreach($this->config->routes->get as $endpoint) {
            $this->app->get($endpoint->url, $endpoint->controller);
        }
    }

    /**
     * Loads the application configuration settings.
     *
     * All system configuration settings should be managed by this function.
     *
     * @return void
     */
    public function loadConfig()
    {
        // Configure Silex Instance
        $this->app['debug'] = true;

        // We do not want to use the default File system session handler.
        $this->app['session.storage.handler'] = null;

        $this->config = [];

        $this->config = json_decode( file_get_contents( __DIR__ . '/config/routes.json' ) );
    }

    /**
     * Sets up the Twig Service Provider for Silex.
     *
     * @return void
     */
    public function startRenderEngine()
    {
        $this->app->register(
            new TwigServiceProvider(),
            array(
                'twig.path' => __DIR__ . '/Views'
            )
        );
    }

    /**
     * Finalizes the startup process and sends the run command to the Silex instance.
     *
     * @return void
     */
    public function startupSite()
    {
        $this->app->run();
    }
}
