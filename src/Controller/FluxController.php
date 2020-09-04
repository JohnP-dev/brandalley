<?php

namespace App\Controller;

use App\Processing\DataLoader\DataLoaderInterface;
use App\Processing\Factory\EndpointFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Processing\Flux;
use Symfony\Component\HttpFoundation\Request;

class FluxController extends AbstractController
{
    private DataLoaderInterface $dataloader;
    private EndpointFactoryInterface $endpointFactory;

    // autowiring
    public function __construct(DataLoaderInterface $dataloader, EndpointFactoryInterface $endpointFactory)
    {
        $this->dataloader = $dataloader;
        $this->endpointFactory = $endpointFactory;
    }

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
    
            $endpoint = $this->endpointFactory->create($type);

            $flux = new Flux();
            $response = $flux->buildResponse($url, $endpoint, $this->dataloader);
        } else $response = [null, null, 0, null];

        return $this->render('flux/index.html.twig', [
            'controller_name' => 'FluxController',
            'response' => $response,
        ]);
    }

    
}