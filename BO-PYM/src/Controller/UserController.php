<?php

namespace App\Controller;

use App\Form\UserEditType;

use App\Entity\Utilisateur;
use DateInterval;
use DateTime;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{


    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $users = $repository->findAll();


        return $this->render("user/index.html.twig", ['users' => $users]);
    }


    /**
     * @Route("/users/me/edit",name="user_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "Adresse e-mail",
                    'class' => 'reg-email rounded form-control'],
                'label' => ' '])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => "Mot de passe",
                    'class' => 'reg-email rounded form-control'],
                'label' => ' '])
            ->add('confirm_password', PasswordType::class, [
                'attr' => [
                    'placeholder' => "Confirmation du mot de passe",
                    'class' => 'reg-username rounded form-control'],
                'label' => ' '])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassWord($user, $user->getPassword());
            $user->setPassword($hash);


            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('entreprises');
        }


        return $this->render("user/edit.html.twig", ['user' => $user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit",name="user_edit_other", methods={"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function edit_other($id, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer)
    {
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        /** @var Utilisateur $user_to_edit */
        $user_to_edit = $repository->find($id);
        if (!$user_to_edit) {
            throw $this->createNotFoundException(
                'No user found for id' / $id
            );
        }

        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user_to_edit);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $password = $user_to_edit->getPassword();
            $hash = $encoder->encodePassWord($user_to_edit, $user_to_edit->getPassword());
            $user_to_edit->setPassword($hash);


            $message = (new Swift_Message('Modification de votre compte'))
                ->setFrom('projetindu6@gmail.com')
                ->setTo($user_to_edit->getEmail())
                ->setBody(
                    $this->renderView(
                        "auth/email/resetpassword_admin.html.twig",
                        ['password' => $password]
                    )
                );

            $manager->flush();

            $mailer->send($message);


            return $this->redirectToRoute('users');
        }

        return $this->render('user/edit.html.twig', ['user' => $user_to_edit, 'user_connected' => $user, 'form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/delete",name="user_delete", methods={"GET", "POST"})
     */
    public function delete($id, EntityManagerInterface $manager)
    {
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $user_to_delete = $repository->find($id);

        $manager->remove($user_to_delete);
        $manager->flush();

        return $this->redirectToRoute('users');
    }

    /**
     * Send an email to confirm the email address.
     *
     * The method accept a Bearer Token generated with /api/auth/register or /api/auth/login.
     * It reset the email verification state to false.
     * It generate an url and send it to the owner of the email address.
     *
     * @Route("/users/me/email_verification", name="auth_email_verification", methods={"POST"})
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function emailVerification(
        Swift_Mailer $mailer
    )
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $user->setIsEmailVerified(false);
        try {
            $user->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (\Exception $e) {
            $user->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('PT1H'));
        $user->setRefreshTokenExpiresAt($expirationDate);
        $em->flush();

        $message = (new Swift_Message('Confirmez votre adresse e-mail.'))
            ->setFrom('account-security-noreply@map-pym.com')
            ->setTo($user->getEmail())
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    "auth/email/confirm_email.html.twig",
                    ['token' => $user->getRefreshToken()]
                )
            );

        $mailer->send($message);

        return $this->redirectToRoute('users');
    }
}
