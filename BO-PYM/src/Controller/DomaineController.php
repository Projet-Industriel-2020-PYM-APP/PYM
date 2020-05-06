<?php

namespace App\Controller;

use App\Entity\Domaine;
use App\Form\DomaineType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomaineController extends AbstractController
{
    /**
     * @Route("/domaine", name="domaine", methods={"GET"})
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Domaine::class);
        $domaine = $repository->findAll();
        $file = '';
        if ($domaine != null) {
            $file = $domaine[0]->getFichier();
        }
        return $this->render('domaine/index.html.twig', [
            'file' => $file
        ]);
    }


    /**
     * @Route("/domaine/{id}/edit",name="domaine_edit", methods={"GET", "POST"})
     */
    public function edit($id, Request $request, FileUploader $fileUploader, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Domaine::class);
        $domaine = $repository->find($id);
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = new File($domaine->getFichier());
            $filename = $fileUploader->upload($file, "domaine", 'domaine');
            $domaine->setFichier($filename);
            $manager->persist($domaine);
            $manager->flush();
            return $this->redirectToRoute('domaine');
        }

        return $this->render("domaine/edit.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
