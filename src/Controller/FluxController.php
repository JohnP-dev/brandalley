<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Processing\Flux;
use Symfony\Component\HttpFoundation\Request;

class FluxController extends AbstractController
{
    /**
     * @Route("/flux", name="flux")
     * Get Request and extract url, and type file (xml or json)
     * Construct URL
     * Filter datas
     */
    public function index(Request $request)
    {
        $response = null; 
        if($request->request->count() > 0){
            //get and build URL
            $url = $request->request->get('url');
            $type = $request->request->get('type');  
            //dump($type) ;                      
            $flux = new Flux();
            $response = $flux->buildResponse($url, $type);
        } else $response = [null, null, 0, null];

        return $this->render('flux/index.html.twig', [
            'controller_name' => 'FluxController',
            'response' => $response,
        ]);
    }

    
}