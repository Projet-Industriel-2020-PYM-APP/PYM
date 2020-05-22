<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Form\PosteType;
use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use App\Repository\PosteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferenceTableController extends AbstractController
{
    private $activiteRepository;
    private $posteRepository;

    public function __construct(ActiviteRepository $activiteRepository, PosteRepository $posteRepository)
    {
        $this->posteRepository = $posteRepository;
        $this->activiteRepository = $activiteRepository;
    }

    /**
     * @Route("/tables_reference", name="reference_tables", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index()
    {
        return $this->render('reference_table/index.html.twig', [
            'activites' => $this->activiteRepository->findAll(),
            'postes' => $this->posteRepository->findAll()
        ]);
    }

    /**
     * @Route("tables_reference/activite/add",name="reference_table_add_activite", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add_activite(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $activite = new Activite;
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($activite);
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/activite/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("tables_reference/activite/{id}/edit",name="reference_table_edit_activite", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Activite $activite
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit_activite(Activite $activite, Request $request)
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/activite/add.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("tables_reference/poste/add",name="reference_table_add_poste", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add_poste(Request $request)
    {
        $poste = new Poste;
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($poste);
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/poste/add.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/tables_reference/activite/{id}/delete",name="reference_table_delete_activite", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Activite $activite
     * @return RedirectResponse
     */
    public function delete_activite(Activite $activite)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($activite);
        $manager->flush();

        return $this->redirectToRoute('reference_tables');
    }


    /**
     * @Route("tables_reference/poste/{id}/edit",name="reference_table_edit_poste", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Poste $poste
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit_poste(Poste $poste, Request $request)
    {
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/poste/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tables_reference/poste/{id}/delete",name="reference_table_delete_poste", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Poste $poste
     * @return RedirectResponse
     */
    public function delete_poste(Poste $poste)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($poste);
        $manager->flush();

        return $this->redirectToRoute('reference_tables');
    }
}
