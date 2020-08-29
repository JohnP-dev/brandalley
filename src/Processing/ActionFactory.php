<?php
namespace App\Processing;

use App\Entity\Action;

class ActionFactory implements ActionFactoryInterface
{
    /**
     * Undocumented function
     *
     * @param array $data
     * @return Action
     * The Main methode to create One Action
     * It's useful for both XML and JSON filter
     */
    public function createAction(array $data): Action
    {
        $action = new Action();
            $action->setIndex($data["Index"]);
            $action->setId($data["Id"]);
            $action->setLabel($data["Label"]);
            $action->setFrontLabel($data["FrontLabel"]);
            $action->setPosition($data["Position"]);
            $action->setPriority($data["Priority"]);
            $action->setBeginDate($data["BeginDate"]);
            $action->setEndDate($data["EndDate"]);
            $action->setHtmlContent($data["HtmlContent"]);
        return $action;
    
    }
}
