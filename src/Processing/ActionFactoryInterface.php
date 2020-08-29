<?php
namespace App\Processing;

use App\Entity\Action;

interface ActionFactoryInterface {

    public function createAction(array $data): Action;
}
