<?php

namespace App\Processing\DataLoader;


class DataLoader implements DataLoaderInterface
{
    public function load(string $url)
    {
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
          throw new \Exception('Url not found...or Network problem');
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
