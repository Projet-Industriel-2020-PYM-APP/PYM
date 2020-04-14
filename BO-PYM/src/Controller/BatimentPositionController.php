<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\BatimentPosition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BatimentPositionController extends AbstractController
{
    /**
     * @Route("/batiment_position/lister", name="get")
     */
    public function index()
    {
        return $this->render('batiment_position/index.html.twig', [
            'controller_name' => 'BatimentPositionController',
        ]);
    }

    /**
     * @Route("/api/batiment_position")
     *
     * return array;
     */
    public function SendAllBatimentPositionAction()
    {
        $batimentPositions = $this->getDoctrine()->getRepository(BatimentPosition::class)->findAll();
        $arrayCollection = array();
        foreach ($batimentPositions as $item) {
            array_push($arrayCollection, array(
                'idBatiment' => $item->getIdBatiment(),
                'latitude' => $item->getLatitude(),
                'longitude' => $item->getLongitude(),
            ));
        }
        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
