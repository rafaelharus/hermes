<?php

namespace Hermes;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

/**
 * @codeCoverageIgnore
 */
class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__.'/../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__.'/../src/'.str_replace('\\', '/', __NAMESPACE__),
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }
}
