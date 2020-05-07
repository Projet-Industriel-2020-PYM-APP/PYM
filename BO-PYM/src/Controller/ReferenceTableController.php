<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Form\PosteType;
use App\Entity\Activite;
use App\Form\ActiviteType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferenceTableController extends AbstractController
{
    /**
     * @Route("/tables_reference", name="reference_tables", methods={"GET"})
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Activite::class);
        $activites = $repository->findAll();

        $repo = $this->getDoctrine()->getRepository(Poste::class);
        $postes = $repo->findAll();

        return $this->render('reference_table/index.html.twig', [
            'activites' => $activites,
            'postes' => $postes
        ]);
    }

    /**
     * @Route("tables_reference/activite/add",name="reference_table_add_activite", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add_activite(Request $request, EntityManagerInterface $manager)
    {
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
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit_activite($id, Request $request, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Activite::class);
        $activite = $repository->findOneBy(['id' => $id]);

        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$manager->persist($activite);
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/activite/add.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("tables_reference/poste/add",name="reference_table_add_poste", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add_poste(Request $request, EntityManagerInterface $manager)
    {
        $poste = new Poste;
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($poste);
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/poste/add.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/tables_reference/activite/{id}/delete",name="reference_table_delete_activite", methods={"GET", "POST"})
     * @param EntityManagerInterface $manager
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete_activite(EntityManagerInterface $manager, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Activite::class);
        $activite_to_delete = $repository->findOneBy(['id' => $id]);

        $manager->remove($activite_to_delete);
        $manager->flush();

        return $this->redirectToRoute('reference_tables');
    }


    /**
     * @Route("tables_reference/poste/{id}/edit",name="reference_table_edit_poste", methods={"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit_poste($id, Request $request, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Poste::class);
        $poste = $repository->findOneBy(['id' => $id]);

        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$manager->persist($activite);
            $manager->flush();
            return $this->redirectToRoute('reference_tables');
        }
        return $this->render('entreprise/poste/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tables_reference/poste/{id}/delete",name="reference_table_delete_poste", methods={"GET", "POST"})
     * @param EntityManagerInterface $manager
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete_poste(EntityManagerInterface $manager, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Poste::class);
        $poste_to_delete = $repository->findOneBy(['id' => $id]);

        $manager->remove($poste_to_delete);
        $manager->flush();

        return $this->redirectToRoute('reference_tables');
    }
}
