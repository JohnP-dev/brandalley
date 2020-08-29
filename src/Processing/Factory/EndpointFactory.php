<?php

namespace App\Processing\Factory;

use App\Processing\ActionFactoryInterface;
use App\Processing\Endpoint\JsonEndpoint;
use App\Processing\Endpoint\XmlEndpoint;

class EndpointFactory implements EndpointFactoryInterface
{
    private ActionFactoryInterface $actionFactory;

    public function __construct(ActionFactoryInterface $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function create(string $type)
    {
        switch($type) {
            case 'xml':
                return new XmlEndpoint($this->actionFactory);
            case 'json':
                return new JsonEndpoint($this->actionFactory);
            default:
                throw new \Exception('No type file chosen...');
        }
    }
}
