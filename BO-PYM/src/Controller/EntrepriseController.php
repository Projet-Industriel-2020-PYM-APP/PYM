<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Contact;
use App\Entity\Entreprise;
use App\Entity\Poste;
use App\Form\ActiviteType;
use App\Form\ContactType;
use App\Form\EntrepriseType;
use App\Form\PosteType;
use App\Repository\ActiviteRepository;
use App\Repository\BureauRepository;
use App\Repository\ContactCategorieRepository;
use App\Repository\ContactRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\PosteRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

header("Access-Control-Allow-Origin: *");

class EntrepriseController extends AbstractController
{
    private $entrepriseRepository;
    private $posteRepository;
    private $fileUploader;
    private $activiteRepository;
    private $contactRepository;
    private $bureauRepository;
    private $contactCategorieRepository;

    public function __construct(
        EntrepriseRepository $entrepriseRepository,
        ActiviteRepository $activiteRepository,
        PosteRepository $posteRepository,
        ContactRepository $contactRepository,
        BureauRepository $bureauRepository,
        ContactCategorieRepository $contactCategorieRepository,
        FileUploader $fileUploader
    )
    {
        $this->entrepriseRepository = $entrepriseRepository;
        $this->activiteRepository = $activiteRepository;
        $this->posteRepository = $posteRepository;
        $this->bureauRepository = $bureauRepository;
        $this->fileUploader = $fileUploader;
        $this->contactRepository = $contactRepository;
        $this->contactCategorieRepository = $contactCategorieRepository;
    }

    /**
     * @Route("/entreprises", name="entreprises", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index()
    {
        return $this->render(
            "entreprise/index.html.twig",
            ['entreprises' => $this->entrepriseRepository->findAll()]
        );
    }

    /**
     * @Route("/entreprises/add",name="entreprise_add", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $entreprise = new Entreprise();

        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('activites')->getData() !== null) {
                $activite = $this->activiteRepository->findOneBy(['Nom' => $form->get('activites')->getData()->getNom()]);
                if (!$activite) {
                    throw $this->createNotFoundException('No activity found');
                }
                $entreprise->addActivite($activite);
            }

            $file = $entreprise->getLogo();
            $nom_entreprise = $entreprise->getNom();
            for ($i = 0, $size = strlen($nom_entreprise); $i < $size; $i++) {
                if ($nom_entreprise[$i] == " ") {
                    $nom_entreprise[$i] = "_";
                }
            }
            $filename = $this->fileUploader->upload($file, $nom_entreprise, 'logos');
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
     * @IsGranted("ROLE_ADMIN")
     * @param Entreprise $entreprise
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Entreprise $entreprise, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $file = $entreprise->getLogo();
        $entreprise->setLogo(new File('uploads/logos/' . $file));
        $old_value = $entreprise->getLogo();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $new_file = $form->get('Logo')->getData();
            if ($new_file !== null) {
                $path = "uploads/logos/" . $file;
                if ($file && file_exists($path)) {
                    unlink($path);
                }
                $nom_entreprise = $entreprise->getNom();
                $filename = $this->fileUploader->upload($new_file, $nom_entreprise, 'logos');
                $entreprise->setLogo($filename);
            } else {
                $entreprise->setLogo($old_value);
            }

            if ($form->get('activites')->getData() !== null) {
                $entreprise->addActivite($form->get('activites')->getData());
            }

            $manager->flush();

            return $this->redirectToRoute('entreprises');
        }

        return $this->render('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
            'file' => $file
        ]);
    }

    /**
     * @Route("/entreprises/{id}",name="entreprise_show", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Entreprise $entreprise
     * @return Response
     */
    public function show(Entreprise $entreprise)
    {
        $file = $entreprise->getLogo();
        $contacts = $entreprise->getContact();
        $activites = $entreprise->getActivites();

        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
            'file' => $file,
            'contacts' => $contacts,
            'activites' => $activites
        ]);
    }

    /**
     * @Route("/entreprises/{id}/contact/add",name="entreprise_add_contact", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Entreprise $entreprise
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add_contact(Entreprise $entreprise, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $contact = new Contact;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('poste')->getData() !== null) {
                $poste = $this->posteRepository->findOneBy(['Nom' => $form->get('poste')->getData()->getNom()]);
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
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add_poste(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
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
     * @IsGranted("ROLE_ADMIN")
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
            return $this->redirectToRoute('entreprises');
        }
        return $this->render('entreprise/activite/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/entreprises/{id_ent}/contact/{id}/edit",name="entreprise_edit_contact", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id_ent
     * @param Contact $contact
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit_contact(int $id_ent, Contact $contact, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $entreprise = $this->entrepriseRepository->find($id_ent);

        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('poste')->getData() !== null) {
                $poste = $this->posteRepository->findOneBy(['Nom' => $form->get('poste')->getData()->getNom()]);
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
     * @Route("/entreprises/{id_ent}/contact/{id}/delete",name="entreprise_delete_contact", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id_ent
     * @param Contact $contact
     * @return RedirectResponse
     */
    public function delete_contact(int $id_ent, Contact $contact)
    {
        $manager = $this->getDoctrine()->getManager();
        $entreprise = $this->entrepriseRepository->find($id_ent);
        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }
        $contactCategorie = $this->contactCategorieRepository->findOneBy(['contact'=> $contact]);
        if ($contactCategorie) {
            $manager->remove($contactCategorie);
        }

        $manager->remove($contact);
        $manager->flush();

        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id_ent}/contact/{id_cont}/poste/{id}/delete",name="entreprise_delete_poste", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param $id_ent
     * @param $id_cont
     * @param Poste $poste
     * @return RedirectResponse
     */
    public function delete_poste(int $id_ent, int $id_cont, Poste $poste)
    {
        $manager = $this->getDoctrine()->getManager();
        $entreprise = $this->entrepriseRepository->find($id_ent);
        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }

        $contact = $this->contactRepository->find($id_cont);
        if (!$contact) {
            throw $this->createNotFoundException(
                'No contact found for id' / $id_cont
            );
        }

        $contact->removePoste($poste);
        $manager->flush();
        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id_ent}/activite/{id}/delete",name="entreprise_delete_activite", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param $id_ent
     * @param Activite $activite
     * @return RedirectResponse
     */
    public function delete_activite($id_ent, Activite $activite)
    {
        $manager = $this->getDoctrine()->getManager();
        $entreprise = $this->entrepriseRepository->find($id_ent);
        if (!$entreprise) {
            throw $this->createNotFoundException(
                'No entreprise found for id' / $id_ent
            );
        }
        $entreprise->removeActivite($activite);
        $manager->flush();
        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/entreprises/{id}/delete",name="entreprise_delete", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Entreprise $entreprise
     * @return RedirectResponse
     */
    public function delete(Entreprise $entreprise)
    {
        $manager = $this->getDoctrine()->getManager();

        $contacts = $this->contactRepository->findBy(['entreprise' => $entreprise]);
        foreach ($contacts as $contact) $manager->remove($contact);

        $bureaux = $this->bureauRepository->findBy(['entreprise' => $entreprise]);
        foreach ($bureaux as $bureau) $manager->remove($bureau);

        $file = $entreprise->getLogo();
        $path = "uploads/logos/" . $file;
        if ($file && file_exists($path)) {
            unlink($path);
        }

        $manager->remove($entreprise);
        $manager->flush();

        return $this->redirectToRoute('entreprises');
    }

    /**
     * @Route("/api/entreprises", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return JsonResponse
     */
    public function SendAllEntrepriseAction()
    {
        $entreprises = $this->entrepriseRepository->findAll();
        return new JsonResponse($entreprises);
    }

    /**
     * @Route("/api/contacts", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return JsonResponse
     */
    public function SendAllContactAction()
    {
        $contacts = $this->contactRepository->findAll();
        return new JsonResponse($contacts);
    }
}