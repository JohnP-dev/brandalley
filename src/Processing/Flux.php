<?php
namespace App\Processing;
use App\Entity\Actions;
use Symfony\Component\DomCrawler\Crawler;
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
        $crawler = new Crawler();
        $crawler->addXmlContent($content);
        $domElement = null;
        foreach($crawler as $domEl) $domElement = $domEl;
        $allActionsNodes = $domElement->getElementsByTagName("Actions");
        $i = 1;
        $datas = array();
        foreach($allActionsNodes as $actions){
            $data[] = $i;
            $data[] = $actions->getElementsByTagName("Id")[0]->nodeValue;
            $data[] = $actions->getElementsByTagName("Label")[0]->nodeValue;
            $data[] = $actions->getElementsByTagName("FrontLabel")[0]->nodeValue;
            $data[] = $actions->getElementsByTagName("Position")[0]->nodeValue;
            $data[] = $actions->getElementsByTagName("Priority")[0]->nodeValue;
            $data[] = $actions->getElementsByTagName("Metadata")[0]->firstChild->childNodes[1]->nodeValue;
            $data[] = $actions->getElementsByTagName("HtmlContent")[0]->nodeValue;

            $action = $this->createOneAction($data);
            array_push($datas, $action);
            $i++;
        }
        return $datas;
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
            $action->setIndex($data[0]);
            $action->setId($data[1]);
            $action->setLabel($data[2]);
            $action->setFrontLabel($data[3]);
            $action->setPosition($data[4]);
            $action->setPriority($data[5]);
            $action->setBeginDate($data[6]);
            $action->setHtmlContent($data[7]);
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