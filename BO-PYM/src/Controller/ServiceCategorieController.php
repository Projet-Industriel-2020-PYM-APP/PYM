<?php

namespace App\Controller;

use App\Entity\ServiceCategorie;
use App\Form\ServiceCategorieType;
use App\Repository\ServiceCategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/service/categorie")
 */
class ServiceCategorieController extends AbstractController
{
    /**
     * @Route("/", name="service_categorie_index", methods={"GET"})
     */
    public function index(ServiceCategorieRepository $serviceCategorieRepository): Response
    {
        return $this->render('service_categorie/index.html.twig', [
            'service_categories' => $serviceCategorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="service_categorie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $serviceCategorie = new ServiceCategorie();
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($serviceCategorie);
            $entityManager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render('service_categorie/new.html.twig', [
            'service_categorie' => $serviceCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_categorie_show", methods={"GET"})
     */
    public function show(ServiceCategorie $serviceCategorie): Response
    {
        return $this->render('service_categorie/show.html.twig', [
            'service_categorie' => $serviceCategorie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ServiceCategorie $serviceCategorie): Response
    {
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render('service_categorie/edit.html.twig', [
            'service_categorie' => $serviceCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ServiceCategorie $serviceCategorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serviceCategorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serviceCategorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_categorie_index');
    }
}
