<?php

namespace App\Controller;

use App\Entity\Domaine;
use App\Form\DomaineType;
use App\Repository\DomaineRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DomaineController extends AbstractController
{
    private $fileUploader;
    private $domaineRepository;
    public function __construct(
        FileUploader $fileUploader,
        DomaineRepository $domaineRepository
    ) {
        $this->fileUploader = $fileUploader;
        $this->domaineRepository = $domaineRepository;
    }

    /**
     * @Route("/domaine", name="domaine", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index()
    {
        $domaine = $this->domaineRepository->findAll();
        $file = '';
        if ($domaine !== null) {
            $file = $domaine[0]->getFichier();
        }
        return $this->render('domaine/index.html.twig', [
            'file' => $file
        ]);
    }


    /**
     * @Route("/domaine/{id}/edit",name="domaine_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(int $id, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $domaine = $this->domaineRepository->find($id);
        if (is_null($domaine)) {
            $domaine = new Domaine();
        }
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('Fichier')->getData();
            $filename = $this->fileUploader->upload($file, "domaine", 'domaine', false);
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
