<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class VirtualKernel
 * @package App
 */
class VirtualKernel extends Kernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @var string
     */
    protected $name;

    /**
     * VirtualKernel constructor.
     * @param $environment
     * @param $debug
     * @param $name
     */
    public function __construct($environment, $debug, $name)
    {
        $this->name = $name;

        parent::__construct($environment, $debug);
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->name.'/'.$this->environment;
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log/'.$this->name;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array($this->environment, $this->debug, $this->name));
    }

    /**
     * @param $data
     */
    public function unserialize($data)
    {
        [$environment, $debug, $name] = \unserialize($data, array('allowed_classes' => false));
        $this->__construct($environment, $debug, $name);
    }

    /**
     * @return \Generator|iterable|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles()
    {
        $commonBundles = require $this->getProjectDir().'/config/bundles.php';
        $kernelBundles = require $this->getProjectDir().'/config/' . $this->name . '/bundles.php';

        foreach (array_merge($commonBundles, $kernelBundles) as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return $this->name.ucfirst($this->environment).($this->debug ? 'Debug' : '').'ProjectContainer';
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @throws
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->addResource(new FileResource($this->getProjectDir().'/config/'.$this->name.'/bundles.php'));
        $this->doConfigureContainer($container, $loader);
        $this->doConfigureContainer($container, $loader, $this->name);
    }

    /**
     * @param RouteCollectionBuilder $routes
     * @throws
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $this->doConfigureRoutes($routes);
        $this->doConfigureRoutes($routes, $this->name);
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     * @param string|null $name
     * @throws \Exception
     */
    private function doConfigureContainer(ContainerBuilder $container, LoaderInterface $loader, string $name = null): void
    {
        $confDir = $this->getProjectDir().'/config'.($name ? '/'.$name : '');
        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{'.$this->environment.'}/**/*'.self::CONFIG_EXTS, 'glob');
    }

    /**
     * @param RouteCollectionBuilder $routes
     * @param string|null $name
     * @throws \Symfony\Component\Config\Exception\LoaderLoadException
     */
    private function doConfigureRoutes(RouteCollectionBuilder $routes, string $name = null): void
    {
        $confDir = $this->getProjectDir().'/config'.($name ? '/'.$name : '');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}