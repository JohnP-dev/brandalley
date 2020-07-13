<?php
namespace App\Processing;
use App\Entity\Actions;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\Length;

class Flux
{
/**
     * Undocumented function
     * @param string $url
     * @param string $type
     * @return array
     * Return datas as a list of any Actions found
     */
    public function buildResponse(string $url, string $type){
        $errors = null;
        $index = 0;
        $datas = array();
        $fullUrl = $this->buildUrl($url, $type); 
        if($content = $this->loadDatas($fullUrl)){
            switch ($type){
                case "xml":
                    if(@simplexml_load_string($content) !== false){
                        $datas = $this->filterActionsXML($content);
                        if(count($datas) == 0) $errors = "No item found...";
                    } else $errors = "The chosen file does not contain XML...";
                    break;

                case "json":
                    $index = 1;
                    if(@json_decode($content, true) !== null){
                        $datas = $this->filterActionsJSON($content);
                        if(count($datas) == 0) $errors = "No item found...";
                    } else $errors = "The chosen file does not contain JSON...";
                    break;
                default: 
                    $errors = "No type file chosen...";
            }  
        } else $errors = "Url not found...or Network problem"; 
        return array($datas, $url, $index, $errors);
    }
    /**
     * Parse URL to build the specific URL such as:
     * URL without a main domain root
     * URL with https://www.brandalley.fr/ domain root 
     */
    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $type
     * @return string $url
     */
    public function buildUrl(string $url, string $type){
        $link['xml'] = "http://brandalley-frontapi-preview-frfr.sparkow.net/";
        $link['json'] = "http://brandalley-frontapi-preview-frfr.sparkow.net/json/";
        $urlBrand = "https://www.brandalley.fr/";
        //URL for xml file
        $list = explode($urlBrand, $url);
        if(count($list) > 1){ //if url begin with https://www.brandalley.fr/
            $url = $link[$type].$list[1];
        }else{
            $list = explode("http", $url);
            if(count($list) == 1){ //if url not contain any domain name
              $url = $link[$type].$list[0];
           }
        }
        return $url;
    }
   
    /**
     * Undocumented function
     *
     * @param string $content
     * @return array
     * XML Filter
     * Find All tagName "Actions" 
     * With each Action get thier informations
     * Return that list
     */
    public function filterActionsXML(string $content ){
    
        $content = \str_replace(" xmlns=\"Compario.FrontAPI.ContentModels\"", "", $content);
        $crawler = new Crawler();
        $crawler->addXmlContent($content);
        
        $domElement = null;
        foreach($crawler as $domEl) $domElement = $domEl;
        $xpath = new \DOMXpath($domElement->ownerDocument);
        
        $allActionsNodes = $xpath->query(".//MerchandisingAction");
        //$allActionsNodes2 = $xpath->query(".//Metadata");
        //dump($domElement->ownerDocument);
        //dump($domElement);
        dump($allActionsNodes);
        

        
        
        $i = 0;
        $datas = array();
        
        for($i = 0; $i < $allActionsNodes->length; $i++){
            
            $actionNode=$allActionsNodes->item($i);
           
            $data = array();
            $data["Index"] = $i+1;
            $data["Id"] = $this->GetElement($xpath, $actionNode, ".//Id");
            $data["Label"] = $this->GetElement($xpath, $actionNode, ".//Label");
            $data["FrontLabel"] = $this->GetElement($xpath, $actionNode, ".//FrontLabel");
            $data["Position"] = $this->GetElement($xpath, $actionNode, ".//Position");
            $data["Priority"] = $this->GetElement($xpath, $actionNode, ".//Priority");
            $data["BeginDate"] =$this->GetElement($xpath, $actionNode, ".//Meta/Value[../Label/text()='BeginDate']");

            $data["EndDate"] = $this->GetElement($xpath, $actionNode, ".//Meta/Value[../Label/text()='EndDate']");

            $data["HtmlContent"] = $this->GetElement($xpath, $actionNode, ".//HtmlContent");
            

            
            
            
            

           
                $action = $this->createOneAction($data);
                array_push($datas, $action);
            
            }
            return $datas;
    }

    private function GetElement($xpath, $node, $searchingPath){

        $nodeList = $xpath->query($searchingPath, $node);
        //dump($nodeList[0]);
        // dump($nodeList);
        return (isset($nodeList[0]))?$nodeList[0]->nodeValue:"";
    }
    
    
    
    // Creer l'arbre du fichier XML :
    public function drawTree($myarray, $level = 0){
        foreach($myarray as $key => $value){
            if (is_object($value)) $value = (array)$value;
            if (is_array($value)) {
                echo '<div style="padding-left: ' . ($level * 20) . 'px;">[' . $key . ']</div>';
                $this->drawTree($value, $level + 1);
            } else 
                echo '<div style="padding-left: ' . ($level * 20) . 'px;">' . $key . ' = <b>' . $value . '</b></div>';
        }
    }

    //Fonction affichage recursive du XML:
    public function recurseXmlDom($node, &$vals, $parent="") {
        $child_count = -1; # Not realy needed.
        $arr = array();
        foreach ($node->childNodes as $child) {
           // if (in_array($child->nodeName,$arr)) {
            //         $child_count++;
            // } else $child_count=0;
            $arr[] = $child->nodeName;
            $k = ($parent == "") ? $child->nodeName." " : $parent." ".$child->nodeName." ";
            $this->recurseXmlDom($child, $vals, $k);
            $vals[$k]= (string)$child->nodeValue;
        }
    }
    /**
     * Undocumented function
     *
     * @param string $content
     * @return array
     * JSON Filter
     * Find all "Actions" Key through the file
     * Get those informations and stock them for each "Actions"
     */
    public function filterActionsJSON(string  $content){
        //Convert to be load as json
        $json_content = json_decode($content, true);
        $allActions = array();
        $this->findActionsJson($json_content, "Actions", $allActions);
        $i = 1;
        $datas = array();
        //Create each action
        foreach($allActions as $actions){
            $data = array($i, $actions[0]['Id'], $actions[0]['Label'], $actions[0]['FrontLabel'], 
                            $actions[0]['Position'], $actions[0]['Priority'], $actions[0]['Metadata'][0]['Value'],
                            $actions[0]['HtmlContent']);
            $action = $this->createOneAction($data);
            array_push($datas, $action);
            $i++;
        }
        return $datas;
    }
    /**
     * Undocumented function
     *
     * @param array $data
     * @return Actions
     * The Main methode to create One Action
     * It's useful for both XML and JSON filter
     */
    public function createOneAction(array $data){
        $action = new Actions();
            $action->setIndex($data["Index"]);
            $action->setId($data["Id"]);
            $action->setLabel($data["Label"]);
            $action->setFrontLabel($data["FrontLabel"]);
            $action->setPosition($data["Position"]);
            $action->setPriority($data["Priority"]);
            $action->setBeginDate($data["BeginDate"]);
            $action->setendDate($data["EndDate"]);
            $action->setHtmlContent($data["HtmlContent"]);
        return $action;
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
    public function findActionsJson(array $node, string $index, array &$actions) {
        foreach ($node as $key => $value) {
            if($key === $index){
                $actions[] = $value;
            }else if(is_array($value)){
                $this->findActionsJson($value, $index, $actions);
            }      
        }
    } 
    /**
     * Undocumented function
     *
     * @param string $url
     * @return string $content
     * Main methode file reader for XML and JSON files
     */
    public function loadDatas(string $url){
	    //user agent is very necessary, otherwise some websites like google.com wont give zipped content
            $opts = array(
              'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-Encoding: gzip\r\n"
              )
            );
	    $context = stream_context_create($opts);
        $content = @file_get_contents($url ,false,$context);
        if ( $content === false) {
            return false;
        }else{
            //If http response header mentions that content is gzipped, then uncompress it
	        foreach($http_response_header as $c => $h){
	        	if(stristr($h, 'content-encoding') and stristr($h, 'gzip')){
	        		//Now lets uncompress the compressed data
	        		$content = gzinflate( substr($content,10,-8) );
	        	}
	        }    
        }
        return $content;
    }
}
?>