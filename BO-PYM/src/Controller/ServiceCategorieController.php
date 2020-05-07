<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\ServiceCategorie;
use App\Form\ServiceCategorieType;
use App\Form\ServiceType;
use App\Repository\ServiceCategorieRepository;
use App\Repository\ServiceRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceCategorieController extends AbstractController
{
    /**
     * @Route("/service_categorie", name="service_categorie_index", methods={"GET"})
     * @param ServiceCategorieRepository $serviceCategorieRepository
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function index(ServiceCategorieRepository $serviceCategorieRepository, ServiceRepository $serviceRepository)
    {
        return $this->render('service_categorie/index.html.twig', [
            'categories' => $serviceCategorieRepository->findAll(),
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/service_categorie/new", name="service_categorie_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager, FileUploader $fileUploader)
    {
        $serviceCategorie = new ServiceCategorie();
        $serviceCategorie->setPrimaryColor("#2196f3");
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $fileUploader->upload($imgFile, $originalFilename, 'service_categories');
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
     * @param ServiceCategorie $serviceCategorie
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @param Request $request HTTP Request
     * @return Response
     */
    public function edit(
        ServiceCategorie $serviceCategorie,
        EntityManagerInterface $manager,
        FileUploader $fileUploader,
        Request $request
    )
    {
        $file = $serviceCategorie->getImgUrl();
        $serviceCategorie->setImgUrl(new File($this->getParameter('shared_directory') . 'service_categories/' . $file));
        $form = $this->createForm(ServiceCategorieType::class, $serviceCategorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                unlink($this->getParameter('shared_directory') . 'service_categories/' . $imgFile);
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $fileUploader->upload($imgFile, $originalFilename, 'service_categories');
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
     * @param string $id Categorie ID
     * @param ServiceCategorie $serviceCategorie
     * @param EntityManagerInterface $manager
     * @param ServiceCategorieRepository $serviceCategorieRepository
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    public function delete($id, ServiceCategorie $serviceCategorie, EntityManagerInterface $manager, ServiceCategorieRepository $serviceCategorieRepository, ServiceRepository $serviceRepository)
    {
        $categorie = $serviceCategorieRepository->find($id);

        $services_to_move = $serviceRepository->findBy(['categorie' => $categorie]);
        foreach ($services_to_move as $service) {
            $imgFile = $service->getImgUrl();
            if ($imgFile) {
                unlink($this->getParameter('shared_directory') . 'services/' . $imgFile);
            }
            $manager->remove($service);
        }
        $imgFile = $serviceCategorie->getImgUrl();
        if ($imgFile) {
            unlink($this->getParameter('shared_directory') . 'service_categories/' . $serviceCategorie->getImgUrl());
        }
        $manager->remove($serviceCategorie);
        $manager->flush();

        return $this->redirectToRoute('service_categorie_index');
    }

    /**
     * @Route("/service_categorie/{id}/services/add", name="service_add", methods={"GET","POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param ServiceCategorieRepository $serviceCategorieRepository
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function add_service(
        $id,
        Request $request,
        EntityManagerInterface $manager,
        ServiceCategorieRepository $serviceCategorieRepository,
        FileUploader $fileUploader
    )
    {
        $service = new Service();
        $categorie = $serviceCategorieRepository->find($id);
        $service->setCategorie($categorie);

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $fileUploader->upload($imgFile, $originalFilename, 'services');
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
     * @param Request $request
     * @param Service $service
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit_service(
        Request $request,
        Service $service,
        EntityManagerInterface $manager,
        FileUploader $fileUploader
    )
    {
        $file = $service->getImgUrl();
        $service->setImgUrl(new File($this->getParameter('shared_directory') . 'services/' . $file));
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                unlink($this->getParameter('shared_directory') . 'services/' . $imgFile);
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $fileUploader->upload($imgFile, $originalFilename, 'services');
                $service->setImgUrl($newFilename);
            }

            $manager->flush();

            return $this->redirectToRoute('service_categorie_index');
        }

        return $this->render('service_categorie/service/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/service_categorie/{id_cat}/services/{id}/delete",name="service_delete", methods={"GET"})
     * @param Service $service
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete_service(Service $service, EntityManagerInterface $manager)
    {
        $imgFile = $service->getImgUrl();
        if ($imgFile) {
            unlink($this->getParameter('shared_directory') . 'services/' . $imgFile);
        }

        $manager->remove($service);
        $manager->flush();

        return $this->redirectToRoute('service_categorie_index');
    }

    /**
     * @Route("/api/service_categories", methods={"GET"})
     * @param ServiceCategorieRepository $serviceCategorieRepository
     * @return JsonResponse
     */
    public function get_service_categories(ServiceCategorieRepository $serviceCategorieRepository)
    {
        $serviceCategories = $serviceCategorieRepository->findAll();
        $response = new JsonResponse($serviceCategories);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/services", methods={"GET"})
     * @param ServiceRepository $serviceRepository
     * @return JsonResponse
     */
    public function get_services(ServiceRepository $serviceRepository)
    {
        $services = $serviceRepository->findAll();
        $response = new JsonResponse($services);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
