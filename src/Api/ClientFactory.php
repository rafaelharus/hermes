<?php

namespace Hermes\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $clientConfig = $config['hermes'];

        $client = new \Zend\Http\Client($clientConfig['uri'], $clientConfig['http_client']['options']);
        $client->getRequest()->getHeaders()->addHeaders($clientConfig['headers']);

        return new Client($client, @$clientConfig['service_name'] ?: null, $clientConfig['depth']);
    }
}
