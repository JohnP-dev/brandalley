<?php

namespace App\Processing\DataLoader;

interface DataLoaderInterface 
{
    public function load(string $url);
}
