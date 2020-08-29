<?php
namespace App\Processing\Endpoint;

use App\Constants\ConstantUrls;
use App\Processing\ActionFactoryInterface;

class JsonEndpoint implements EndpointInterface
{
    private const JSON_LINK = 'http://brandalley-frontapi-preview-frfr.sparkow.net/json/';
    
    private ActionFactoryInterface $actionFactory;

    public function __construct(ActionFactoryInterface $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function buildUrl(string $url)
    {    
        //URL for xml file
        $list = explode(ConstantUrls::URL_BRAND, $url);

        if(count($list) > 1){ //if url begin with https://www.brandalley.fr/
            $url = self::JSON_LINK.$list[1];
        } else{
            $list = explode("http", $url);
            if(count($list) == 1){ //if url not contain any domain name
                $url = self::JSON_LINK.$list[0];
            }
        }
        return $url;
    }

    public function filterAction($content)
    {
        $jsonContent = @json_decode($content, true);

        if ($jsonContent !== null)
        {
            $datas = $this->filterActionsJSON($jsonContent);
            if(count($datas) == 0) {
                throw new \Exception('No item found');
            }
            return $datas;
        } else {
            throw new  \Exception('The chosen file does not contain JSON...');
        }
    }

    public function filterAction2($content)
    {
        $jsonContent = @json_decode($content, true);

        if ($jsonContent === null) {
            throw new  \Exception('The chosen file does not contain JSON...');
        }

        $datas = $this->filterActionsJSON($jsonContent);
        if(count($datas) == 0) {
            throw new \Exception('No item found');
        }
        return $datas;
    }

    /**
     * Undocumented function
     *
     * @param  array $content
     * @return array
     * JSON Filter
     * Find all "Actions" Key through the file
     * Get those informations and stock them for each "Actions"
     */
    private function filterActionsJSON( array $jsonContent){
        //Convert to be load as json
        $allActions = array();
        $this->findActionsJson($jsonContent, "Actions", $allActions);
        $i = 0;
        $datas = array();
        //Create each action
        foreach($allActions as $actions){
            list($beginDate, $endDate) = $this->extractDates($actions[0]['Metadata']);

            $data = [
                'Index' => $i,
                'Id' => $actions[0]['Id'],
                'Label' => $actions[0]['Label'],
                'FrontLabel' => $actions[0]['FrontLabel'],
                'Position' =>$actions[0]['Position'],
                'Priority' => $actions[0]['Priority'],
                'HtmlContent' => $actions[0]['HtmlContent'],
                'BeginDate' => $beginDate,
                'EndDate' => $endDate
            ];

            $action = $this->actionFactory->createAction($data);
            array_push($datas, $action);
            $i++;
        }
        return $datas;
    }

    private function extractDates($metadata)
    {
        $beginDate = $endDate = '';

        foreach($metadata as $data) {
            if ($data['Label'] === 'BeginDate') {
                $beginDate = $data['Value'];
            } else if ($data['Label'] === 'EndDate') {
                $endDate = $data['Value'];
            }
        }



        return [$beginDate, $endDate];
    }

    /**
     * Undocumented function
     *
     * @param array $node
     * @param string $index
     * @param array $actions
     * @return void
     * Methode to find Actions in a JSON file
     * Retrun a list of Actions found
     */

    private function findActionsJson(array $node, string $index, array &$actions) {
        foreach ($node as $key => $value) {
            if($key === $index){
                $actions[] = $value;
            } else if(is_array($value)){
                $this->findActionsJson($value, $index, $actions);
            }      
        }
    } 
}
