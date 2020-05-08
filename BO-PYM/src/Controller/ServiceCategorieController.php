<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceCategorie;
use App\Form\ServiceCategorieType;
use App\Form\ServiceType;
use App\Repository\ServiceCategorieRepository;
use App\Repository\ServiceRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceCategorieController extends AbstractController
{
    private $serviceCategorieRepository;
    private $serviceRepository;
    private $fileUploader;

    public function __construct(
        ServiceCategorieRepository $serviceCategorieRepository,
        ServiceRepository $serviceRepository,
        FileUploader $fileUploader
    )
    {
        $this->serviceCategorieRepository = $serviceCategorieRepository;
        $this->serviceRepository = $serviceRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/service_categorie", name="service_categorie_index", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function index()
    {
        return $this->render('service_categorie/index.html.twig', [
            'categories' => $this->serviceCategorieRepository->findAll(),
            'services' => $this->serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/service_categorie/new", name="service_categorie_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $serviceCategorie = new ServiceCategorie();
        $serviceCategorie->setPrimaryColor("#2196f3");
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'service_categories');
                $serviceCategorie->setImgUrl($newFilename);
            }
            $manager->persist($serviceCategorie);
            $manager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render("service_categorie/new.html.twig", ['form' => $form->createView()]);

    }

    /**
     * @Route("/service_categorie/{id}/edit", name="service_categorie_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param ServiceCategorie $serviceCategorie
     * @param Request $request HTTP Request
     * @return Response
     */
    public function edit(
        ServiceCategorie $serviceCategorie,
        Request $request
    )
    {
        $manager = $this->getDoctrine()->getManager();
        $file = $serviceCategorie->getImgUrl();
        $serviceCategorie->setImgUrl(new File($this->getParameter('shared_directory') . 'service_categories/' . $file));
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $path = $this->getParameter('shared_directory') . 'service_categories/' . $imgFile;
                if(file_exists($path)){
                    unlink($path);
                }
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'service_categories');
                $serviceCategorie->setImgUrl($newFilename);
            }

            $manager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render("service_categorie/edit.html.twig", [
            'form' => $form->createView(),
            'service_categorie' => $serviceCategorie,
        ]);
    }

    /**
     * @Route("/service_categorie/{id}/delete", name="service_categorie_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param ServiceCategorie $serviceCategorie
     * @return Response
     */
    public function delete(ServiceCategorie $serviceCategorie)
    {
        $manager = $this->getDoctrine()->getManager();
        $services = $this->serviceRepository->findBy(['categorie' => $serviceCategorie]);
        foreach ($services as $service) {
            $imgFile = $service->getImgUrl();
            $path = $this->getParameter('shared_directory') . 'services/' . $imgFile;
            if($imgFile && file_exists($path)){
                unlink($path);
            }
            $manager->remove($service);
        }
        $imgFile = $serviceCategorie->getImgUrl();
        $path = $this->getParameter('shared_directory') . 'service_categories/' . $imgFile;
        if($imgFile && file_exists($path)){
            unlink($path);
        }
        $manager->remove($serviceCategorie);
        $manager->flush();

        return $this->redirectToRoute('service_categorie_index');
    }

    /**
     * @Route("/service_categorie/{id}/services/add", name="service_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param ServiceCategorie $categorie
     * @param Request $request
     * @return Response
     */
    public function add_service(
        ServiceCategorie $categorie,
        Request $request
    )
    {
        $manager = $this->getDoctrine()->getManager();
        $service = new Service();
        $service->setCategorie($categorie);

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'services');
                $service->setImgUrl($newFilename);
            }

            $manager->persist($service);
            $manager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render('service_categorie/service/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/service_categorie/{id_cat}/services/{id}/edit",name="service_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Service $service
     * @return Response
     */
    public function edit_service(
        Request $request,
        Service $service
    )
    {
        $manager = $this->getDoctrine()->getManager();
        $file = $service->getImgUrl();
        $service->setImgUrl(new File($this->getParameter('shared_directory') . 'services/' . $file));
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $path = $this->getParameter('shared_directory') . 'services/' . $imgFile;
                if(file_exists($path)){
                    unlink($path);
                }
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'services');
                $service->setImgUrl($newFilename);
            }

            $manager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render('service_categorie/service/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/service_categorie/{id_cat}/services/{id}/delete",name="service_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Service $service
     * @return Response
     */
    public function delete_service(Service $service)
    {
        $manager = $this->getDoctrine()->getManager();
        $imgFile = $service->getImgUrl();
        $path = $this->getParameter('shared_directory') . 'services/' . $imgFile;
        if($imgFile && file_exists($path)){
            unlink($path);
        }

        $manager->remove($service);
        $manager->flush();

        return $this->redirectToRoute('service_categorie_index');
    }

    /**
     * @Route("/api/service_categories", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return JsonResponse
     */
    public function get_service_categories()
    {
        $serviceCategories = $this->serviceCategorieRepository->findAll();
        return new JsonResponse($serviceCategories);
    }

    /**
     * @Route("/api/services", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return JsonResponse
     */
    public function get_services()
    {
        $services = $this->serviceRepository->findAll();
        return new JsonResponse($services);
    }
}
