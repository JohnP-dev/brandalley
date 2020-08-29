<?php
namespace App\Processing;
use App\Processing\DataLoader\DataLoaderInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\Length;
use App\Processing\Endpoint\EndpointInterface;

class Flux
{
    /**
     * Undocumented function
     * @param string $url
     * @param string $type
     * @return array
     * Return datas as a list of any Action found
     */
    public function buildResponse(string $url, EndpointInterface $endpoint, DataLoaderInterface $loader){
        $index = 0;
        $datas = array();
        try {
            $fullUrl = $endpoint->buildUrl($url);
            $content = $loader->load($fullUrl);
            $datas = $endpoint->filterAction($content);
            
            return array($datas, $url, $index, null);
        } catch( \Exception $e) {
            return array(null, $url, $index, $e->getMessage());
        }
       
    }

}
?>