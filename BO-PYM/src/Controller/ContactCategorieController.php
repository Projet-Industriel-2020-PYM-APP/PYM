<?php

namespace App\Controller;

use App\Entity\ContactCategorie;
use App\Form\ContactCategorieType;
use App\Repository\ContactCategorieRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactCategorieController extends AbstractController
{
    private $contactCategorieRepository;
    private $fileUploader;

    public function __construct(
        ContactCategorieRepository $contactCategorieRepository,
        FileUploader $fileUploader
    ) {
        $this->contactCategorieRepository = $contactCategorieRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/contact_categories", name="contact_categorie_index", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(): Response
    {
        return $this->render('contact_categorie/index.html.twig', [
            'categories' => $this->contactCategorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/contact_categories/add", name="contact_categorie_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $contactCategorie = new ContactCategorie();
        $form = $this->createForm(ContactCategorieType::class, $contactCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'contact_categories');
                $contactCategorie->setImgUrl($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactCategorie);
            $entityManager->flush();

            return $this->redirectToRoute('contact_categorie_index');
        }

        return $this->render('contact_categorie/new.html.twig', [
            'categorie' => $contactCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contact_categories/{id}/edit", name="contact_categorie_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param ContactCategorie $contactCategorie
     * @return Response
     */
    public function edit(Request $request, ContactCategorie $contactCategorie): Response
    {
        $oldFile = $contactCategorie->getImgUrl();
        $form = $this->createForm(ContactCategorieType::class, $contactCategorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $path = $this->getParameter('shared_directory') . 'contact_categories/' . $oldFile;
                if ($oldFile && file_exists($path)) {
                    unlink($path);
                }
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'contact_categories');
                $contactCategorie->setImgUrl($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contact_categorie_index');
        }

        return $this->render('contact_categorie/edit.html.twig', [
            'categorie' => $contactCategorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/contact_categories/{id}/delete", name="contact_categorie_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param ContactCategorie $contactCategorie
     * @return Response
     */
    public function delete(ContactCategorie $contactCategorie): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $imgFile = $contactCategorie->getImgUrl();
        $path = $this->getParameter('shared_directory') . 'contact_categories/' . $imgFile;
        if ($imgFile && file_exists($path)) {
            unlink($path);
        }
        $entityManager->remove($contactCategorie);
        $entityManager->flush();

        return $this->redirectToRoute('contact_categorie_index');
    }

    /**
     * @Route("/api/contact_categories", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return JsonResponse
     */
    public function fetchContactCategoriesAction()
    {
        $contacts = $this->contactCategorieRepository->findAll();
        return new JsonResponse($contacts);
    }
}
