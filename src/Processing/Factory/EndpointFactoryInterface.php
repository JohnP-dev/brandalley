<?php

namespace App\Processing\Factory;

interface EndpointFactoryInterface
{
    public function create(string $type);
}
