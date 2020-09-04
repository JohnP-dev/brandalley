<?php
namespace App\Processing\Endpoint;

interface EndpointInterface
{
    public function buildUrl(string $url);

    public function filterAction($content);
}
