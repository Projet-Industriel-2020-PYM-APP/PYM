<?php

namespace App\Controller;

use App\Entity\Poste;
use App\Entity\Bureau;
use App\Entity\Contact;
use App\Form\PosteType;
use App\Entity\Activite;
use App\Form\ContactType;
use App\Entity\Entreprise;
use App\Form\ActiviteType;
use App\Form\EntrepriseType;
use App\Service\FileUploader;
use Intervention\Image\ImageManagerStatic as Image;
use App\Form\EntrepriseEditType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

header("Access-Control-Allow-Origin: *");

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/entreprises", name="entreprises", methods={"GET"})
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprises = $repository->findAll();

        return $this->render("entreprise/index.html.twig", ['entreprises' => $entreprises]);
    }

    /**
     * @Route("/entreprises/add",name="entreprise_add", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return RedirectResponse|Response
     */
    public function add(Request $request, EntityManagerInterface $manager, FileUploader $fileUploader)
    {
        $entreprise = new Entreprise();

        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $repo = $this->getDoctrine()->getRepository(Activite::class);
            if ($form->get('activites')->getData() != null) {
                $activite = $repo->findOneBy(['Nom' => $form->get('activites')->getData()->getNom()]);
                if (!$activite) {
                    throw $this->createNotFoundException('No activity found');
                }
                $entreprise->addActivite($activite);
                $manager->persist($activite);
            }

            $file = $entreprise->getLogo();
            $nom_entreprise = $entreprise->getNom();
            for ($i = 0, $size = strlen($nom_entreprise); $i < $size; $i++) {
                if ($nom_entreprise[$i] == " ") {
                    $nom_entreprise[$i] = "_";
                }
            }
            $filename = $fileUploader->upload($file, $nom_entreprise, 'logos');
            //$img=Image::make('uploads/logos/'.$filename);
            //$img->resize(500,500);
            //$img->save('uploads/logos/'.$filename);
            $entreprise->setLogo($filename);


            $manager->persist($entreprise);
            $manager->flush();

            return $this->redirectToRoute('entreprises');
        }

        return $this->render("entreprise/add.html.twig", ['form' => $form->createView()]);
    }


    /**
     * @Route("/entreprises/{id}/edit",name="entreprise_edit", methods={"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return RedirectResponse|Response
     */
    public function edit($id, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise_to_edit = $repository->find($id);

        if (!$entreprise_to_edit) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id
            );
        }
        $file = $entreprise_to_edit->getLogo();
        $entreprise_to_edit->setLogo(new File('uploads/logos/' . $file));
        $old_value = $entreprise_to_edit->getLogo();
        $form = $this->createForm(EntrepriseType::class, $entreprise_to_edit);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $new_file = $form->get('Logo')->getData();
            if ($new_file != null) {
                unlink("uploads/logos/" . $file);
                $nom_entreprise = $entreprise_to_edit->getNom();
                $filename = $fileUploader->upload($new_file, $nom_entreprise, 'logos');
                $entreprise_to_edit->setLogo($filename);
            } else {
                $logo = $entreprise_to_edit->getLogo();
                $entreprise_to_edit->setLogo($old_value);
            }

            if ($form->get('activites')->getData() != null) {
                $entreprise_to_edit->addActivite($form->get('activites')->getData());
            }

            $manager->flush();

            return $this->redirectToRoute('entreprises');
        }

        return $this->render('entreprise/edit.html.twig', ['entreprise' => $entreprise_to_edit, 'form' => $form->createView(), 'file' => $file]);
    }

    /**
     * @Route("/entreprises/{id}",name="entreprise_show", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id);

        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id
            );
        }

        $file = $entreprise->getLogo();

        $contacts = $entreprise->getContact();
        $activites = $entreprise->getActivites();

        return $this->render('entreprise/show.html.twig', ['entreprise' => $entreprise, 'file' => $file, 'contacts' => $contacts, 'activites' => $activites]);
    }

    /**
     * @Route("/entreprises/{id}/contact/add",name="entreprise_add_contact", methods={"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function add_contact($id, Request $request, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id);

        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id
            );
        }

        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $repo = $this->getDoctrine()->getRepository(Poste::class);
            if ($form->get('poste')->getData() != null) {
                $poste = $repo->findOneBy(['Nom' => $form->get('poste')->getData()->getNom()]);
                if (!$poste) {
                    throw $this->createNotFoundException('No poste found ');
                }
                $contact->addPoste($poste);
                $manager->persist($poste);
            }

            $entreprise->addContact($contact);

            $manager->persist($contact);
            $manager->persist($entreprise);


            $manager->flush();

            return $this->redirectToRoute('entreprises');
        }

        return $this->render('entreprise/contact/add.html.twig', ['form' => $form->createView(), 'entreprise' => $entreprise]);
    }

    /**
     * @Route("/entreprises/poste/add",name="entreprise_add_poste", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function add_poste(Request $request, EntityManagerInterface $manager)
    {
        $poste = new Poste;
        $form = $this->createForm(PosteType::class, $poste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($poste);
            $manager->flush();
            return $this->redirectToRoute('entreprises');
        }
        return $this->render('entreprise/poste/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/entreprises/activite/add",name="entreprise_add_activite", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function add_activite(Request $request, EntityManagerInterface $manager)
    {
        $activite = new Activite;
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($activite);
            $manager->flush();
            return $this->redirectToRoute('entreprises');
        }
        return $this->render('entreprise/activite/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/entreprises/{id_ent}/contact/{id_cont}/edit",name="entreprise_edit_contact", methods={"GET", "POST"})
     * @param $id_ent
     * @param $id_cont
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function edit_contact($id_ent, $id_cont, Request $request, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id_ent);

        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }

        $repo = $this->getDoctrine()->getRepository(Contact::class);
        $contact = $repo->find($id_cont);

        if (!$contact) {
            throw $this->createNotFoundException(
                'No contact found for id' / $id_cont
            );
        }


        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('poste')->getData() != null) {
                $repos = $this->getDoctrine()->getRepository(Poste::class);
                $poste = $repos->findOneBy(['Nom' => $form->get('poste')->getData()->getNom()]);
                if (!$poste) {
                    throw $this->createNotFoundException(
                        'No poste found'
                    );
                }
                $contact->addPoste($form->get('poste')->getData());
            }

            $manager->flush();
            return $this->redirectToRoute('entreprises');
        }

        return $this->render('entreprise/contact/edit.html.twig', ['entreprise' => $entreprise, 'contact' => $contact, 'form' => $form->createView()]);
    }

    /**
     * @Route("/entreprises/{id_ent}/contact/{id_cont}/delete",name="entreprise_delete_contact", methods={"GET", "POST"})
     * @param $id_ent
     * @param $id_cont
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function delete_contact($id_ent, $id_cont, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id_ent);

        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }

        $repo = $this->getDoctrine()->getRepository(Contact::class);
        $contact = $repo->find($id_cont);

        if (!$contact) {
            throw $this->createNotFoundException(
                'No contact found for id' / $id_cont
            );
        }

        $manager->remove($contact);
        $manager->flush();

        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id_ent}/contact/{id_cont}/poste/{id}/delete",name="entreprise_delete_poste", methods={"GET", "POST"})
     * @param $id_ent
     * @param $id_cont
     * @param $id
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function delete_poste($id_ent, $id_cont, $id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id_ent);
        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }

        $repo = $this->getDoctrine()->getRepository(Contact::class);
        $contact = $repo->find($id_cont);

        if (!$contact) {
            throw $this->createNotFoundException(
                'No contact found for id' / $id_cont
            );
        }
        $repos = $this->getDoctrine()->getRepository(Poste::class);
        $poste_to_delete = $repos->find($id);
        $contact->removePoste($poste_to_delete);
        $manager->flush();
        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id_ent}/activite/{id}/delete",name="entreprise_delete_activite", methods={"GET", "POST"})
     * @param $id_ent
     * @param $id
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function delete_activite($id_ent, $id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise = $repository->find($id_ent);
        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }
        $repos = $this->getDoctrine()->getRepository(Activite::class);
        $activite_to_delete = $repos->find($id);
        $entreprise->removeActivite($activite_to_delete);
        $manager->flush();
        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id}/delete",name="entreprise_delete", methods={"GET", "POST"})
     */
    public function delete($id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Entreprise::class);
        $entreprise_to_delete = $repository->find($id);

        $repo = $this->getDoctrine()->getRepository(Contact::class);
        $contacts_to_delete = $repo->findBy(['entreprise' => $entreprise_to_delete]);
        for ($i = 0, $size = sizeof($contacts_to_delete) - 1; $i <= $size; $i++) {
            $manager->remove($contacts_to_delete[$i]);
        }

        $repo2 = $this->getDoctrine()->getRepository(Bureau::class);
        $bureaux = $repo2->findBy(['entreprise' => $entreprise_to_delete]);
        for ($i = 0, $size = sizeof($bureaux) - 1; $i <= $size; $i++) {
            $manager->remove($bureaux[$i]);
        }

        unlink("uploads/logos/" . $entreprise_to_delete->getLogo());

        $manager->remove($entreprise_to_delete);
        $manager->flush();

        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/api/entreprises", methods={"GET"})
     *
     * return array;
     */
    public function SendAllEntrepriseAction()
    {
        $entreprises = $this->getDoctrine()->getRepository(Entreprise::class)->findAll();
        $arrayCollection = array();
        foreach ($entreprises as $item) {
            if ($item->getBureaux()[0] == null) {
                array_push($arrayCollection, array(
                    'id' => $item->getId(),
                    'nom' => $item->getNom(),
                    'site_internet' => $item->getSiteInternet(),
                    'nb_salaries' => $item->getNbSalaries(),
                    'telephone' => $item->getTelephone(),
                    'mail' => $item->getMail(),
                    'logo' => $item->getLogo(),
                    'idBatiment' => 0,
                ));
            } else {
                array_push($arrayCollection, array(
                    'id' => $item->getId(),
                    'nom' => $item->getNom(),
                    'site_internet' => $item->getSiteInternet(),
                    'nb_salaries' => $item->getNbSalaries(),
                    'telephone' => $item->getTelephone(),
                    'mail' => $item->getMail(),
                    'logo' => $item->getLogo(),
                    'idBatiment' => $item->getBureaux()[0]->getBatiment()->getId(),
                ));
            }
        }
        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/contacts", methods={"GET"})
     *
     * return array;
     */
    public function SendAllContactAction()
    {
        $contacts = $this->getDoctrine()->getRepository(Contact::class)->findAll();
        $arrayCollection = array();
        foreach ($contacts as $item) {
            array_push($arrayCollection, array(
                'id' => $item->getId(),
                'nom' => $item->getNom(),
                'prenom' => $item->getPrenom(),
                'telephone' => $item->getTelephone(),
                'mail' => $item->getMail(),
                'idEntreprise' => $item->getEntreprise()->getId(),
            ));
        }
        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}