<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Entity\Bureau;
use App\Form\Batiment2Type;
use App\Form\BureauType;
use App\Repository\BatimentRepository;
use App\Repository\BureauRepository;
use App\Repository\EntrepriseRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

header("Access-Control-Allow-Origin: *");

class BatimentController extends AbstractController
{
    private $batimentRepository;
    private $entrepriseRepository;
    private $bureauRepository;
    private $fileUploader;

    public function __construct(
        BatimentRepository $batimentRepository,
        EntrepriseRepository $entrepriseRepository,
        BureauRepository $bureauRepository,
        FileUploader $fileUploader
    ) {
        $this->batimentRepository = $batimentRepository;
        $this->entrepriseRepository = $entrepriseRepository;
        $this->bureauRepository = $bureauRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/batiments", name="batiments", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function index()
    {
        $batiments = $this->batimentRepository->findAll();

        return $this->render('batiment/index.html.twig', [
            'batiments' => $batiments
        ]);
    }

    /**
     * @Route("/batiments/add",name="batiment_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $batiment = new Batiment;

        $form = $this->createForm(Batiment2Type::class, $batiment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'batiments');
                $batiment->setImgUrl($newFilename);
            }

            switch ($batiment->getTypeBatiment()) {
                case "Arret de bus":
                    $batiment->setRepresentation3D("ARRET.babylon");
                    break;
                case "PAV":
                    $batiment->setRepresentation3D("PAV_Poubelles.babylon");
                    break;
                case "IRVE":
                    $batiment->setRepresentation3D("IRVE.babylon");
                    break;
                case "Batiment":
                    $model = $form->get('Representation3D')->getData();
                    $nom_batiment = $batiment->getNom();
                    for ($i = 0, $size = strlen($nom_batiment); $i < $size; $i++) {
                        if ($nom_batiment[$i] == " ") {
                            $nom_batiment[$i] = "_";
                        }
                    }
                    $filename = $this->fileUploader->upload($model, $nom_batiment, 'modeles');
                    $batiment->setRepresentation3D($filename);
                    break;
                case "Forme Paramétrique":
                    //if ($batiment->getFormeParametrique() == null) {
                    //
                    //     return new Response("Veuillez saisir une forme paramétrique.");
                    //}
                    break;
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($batiment);
            $manager->flush();
            return $this->redirectToRoute('batiments');
        }
        return $this->render('batiment/add2.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("batiments/{id}/edit",name="batiment_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Batiment $batiment
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Batiment $batiment, Request $request)
    {
        $old_value = $batiment->getRepresentation3D();
        $oldFile = $batiment->getImgUrl();

        $form = $this->createForm(Batiment2Type::class, $batiment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imgUrl')->getData();

            if ($imgFile) {
                $path = $this->getParameter('shared_directory') . 'service_categories/' . $oldFile;
                if ($oldFile && file_exists($path)) {
                    unlink($path);
                }
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $this->fileUploader->upload($imgFile, $originalFilename, 'batiments');
                $batiment->setImgUrl($newFilename);
            }

            if ($batiment->getTypeBatiment() == "Arret de bus") {
                $batiment->setRepresentation3D("ARRET.babylon");
            }
            if ($batiment->getTypeBatiment() == "PAV") {
                $batiment->setRepresentation3D("PAV_Poubelles.babylon");
            }
            if ($batiment->getTypeBatiment() == "IRVE") {
                $batiment->setRepresentation3D("IRVE.babylon");
            }
            if ($batiment->getTypeBatiment() == "Batiment") {
                $model = $form->get('Representation3D')->getData();
                $nom_batiment = $batiment->getNom();
                for ($i = 0, $size = strlen($nom_batiment); $i < $size; $i++) {
                    if ($nom_batiment[$i] == " ") {
                        $nom_batiment[$i] = "_";
                    }
                }
                if ($model !== null) {
                    $path = "uploads/modeles" . $old_value;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $filename = $this->fileUploader->upload($model, $nom_batiment, 'modeles');
                    $batiment->setRepresentation3D($filename);
                } else {
                    $batiment->setRepresentation3D($old_value);
                }
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            return $this->redirectToRoute('batiments');
        }

        return $this->render('batiment/edit.html.twig', [
            'form' => $form->createView(), 'batiment' => $batiment
        ]);
    }

    /**
     * @Route("batiments/{id}/bureau/add",name="batiment_add_bureau", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Batiment $batiment
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add_bureau(Batiment $batiment, Request $request)
    {
        $bureau = new Bureau;

        $form = $this->createForm(BureauType::class, $bureau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bureau->setBatiment($batiment);
            $entreprise = $bureau->getEntreprise();
            $entreprise->addBureaux($bureau);
            $batiment->addBureaux($bureau);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($bureau);
            $manager->persist($entreprise);
            $manager->persist($batiment);
            $manager->flush();
            return $this->redirectToRoute('batiments');
        }

        return $this->render('batiment/bureau/add.html.twig', [
            'form' => $form->createView(),
            'entreprises' => $this->entrepriseRepository->findAll()
        ]);
    }

    /**
     * @Route("/batiments/{id_bat}/bureau/{id}/edit",name="batiment_edit_bureau", methods={"GET","POST"})
     * @Entity("batiment", expr="repository.find(id_bat)")
     * @IsGranted("ROLE_ADMIN")
     * @param Batiment $batiment
     * @param Bureau $bureau
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit_bureau(Batiment $batiment, Bureau $bureau, Request $request)
    {
        $form = $this->createForm(BureauType::class, $bureau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirectToRoute('batiments');
        }

        return $this->render(
            'batiment/bureau/edit.html.twig',
            [
                'form' => $form->createView(),
                'bureau' => $bureau
            ]
        );
    }

    /**
     * @Route("batiments/{id_bat}/bureau/{id}/delete",name="batiment_delete_bureau", methods={"GET","POST"})
     * @Entity("batiment", expr="repository.find(id_bat)")
     * @IsGranted("ROLE_ADMIN")
     * @param Batiment $batiment
     * @param Bureau $bureau
     * @return RedirectResponse
     */
    public function delete_bureau(Batiment $batiment, Bureau $bureau)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($bureau);
        $manager->flush();

        return $this->redirectToRoute('batiments');
    }

    /**
     * @Route("batiments/{id}/delete",name="batiment_delete")
     * @IsGranted("ROLE_ADMIN")
     * @param Batiment $batiment
     * @return RedirectResponse
     */
    public function delete(Batiment $batiment)
    {
        $manager = $this->getDoctrine()->getManager();
        $bureaux_to_delete = $this->bureauRepository->findBy(['Batiment' => $batiment]);

        for ($i = 0, $size = sizeof($bureaux_to_delete) - 1; $i <= $size; $i++) {
            $manager->remove($bureaux_to_delete[$i]);
        }
        if ($batiment->getTypeBatiment() == "Batiment") {
            $path = "uploads/modeles/" . $batiment->getRepresentation3D();
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $manager->remove($batiment);
        $manager->flush();

        return $this->redirectToRoute('batiments');
    }

    /**
     * @Route("/api/batiments", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @return JsonResponse
     */
    public function sendAllBatimentAction()
    {
        $batiments = $this->batimentRepository->findAll();
        $filteredBatiments = array_filter($batiments, function ($v) {
            return $v->getEtat() === true;
        });
        return new JsonResponse($filteredBatiments);
    }

    /**
     *
     * @Route("/api/bureaux", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @return JsonResponse
     */
    public function sendAllBureauAction()
    {
        $bureaux = $this->bureauRepository->findAll();
        return new JsonResponse($bureaux);
    }
}
