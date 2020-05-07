<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="root", methods={"GET"})
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }
}