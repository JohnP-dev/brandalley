<?php
namespace App\Processing\Endpoint;

use App\Constants\ConstantUrls;
use App\Processing\ActionFactoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class XmlEndpoint implements EndpointInterface
{
    private const LINK_XML ='http://brandalley-frontapi-preview-frfr.sparkow.net/';

    private ActionFactoryInterface $actionFactory;

    public function __construct(ActionFactoryInterface $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    public function buildUrl(string $url)
    {
        $list = explode(ConstantUrls::URL_BRAND, $url);
        if(count($list) > 1){ //if url begin with https://www.brandalley.fr/
            $url = self::LINK_XML.$list[1];
        }else{
            $list = explode("http", $url);
            if(count($list) == 1){ //if url not contain any domain name
              $url = self::LINK_XML.$list[0];
           }
        }
        return $url;
    }

    public function filterAction($content)
    {
        $content = \str_replace(" xmlns=\"Compario.FrontAPI.ContentModels\"", "", $content);
        $crawler = new Crawler();//A injecter plus tard...
        $crawler->addXmlContent($content);

                            
        $crawlerMerchandisings = $crawler->filterXPath('//Actions/MerchandisingAction')->each(function (Crawler $node, $i) {
            
            $nodeId = $node->children()->filter('Id')->text();
            $nodeLabel = $node->children()->filter('Label')->text();
            $nodeFrontLabel = $node->children()->filter('FrontLabel')->text();
            $nodePosition = $node->children()->filter('Position')->text();
            $nodePriority = $node->children()->filter('Priority')->text();
            $nodeBeginDate = $node->children()->filterXPath(".//Meta/Value[../Label/text()='BeginDate']")->text();
            $nodeEndDate = $node->children()->filterXPath(".//Meta/Value[../Label/text()='EndDate']")->text("");
            dump($nodeEndDate);
            $nodeHtmlContent = $node->children()->filter('HtmlContent')->text();
        
            return $this->actionFactory->createAction (array('Index'=> $i,'Id'=>$nodeId, 'Label'=>$nodeLabel, 'FrontLabel'=>$nodeFrontLabel, 'Position'=>$nodePosition, 'Priority'=>$nodePriority, 'BeginDate'=>$nodeBeginDate, 'EndDate'=>$nodeEndDate, 'HtmlContent'=>$nodeHtmlContent)) ;
            
            
        });
       //dump($crawlerMerchandisings);
        //return $crawlerMerchandisings;
       
        return $crawlerMerchandisings;

    } 
    
}
