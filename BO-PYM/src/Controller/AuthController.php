<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use DateInterval;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AuthController extends AbstractController
{

    /**
     * @Route("/utilisateurs/ajout", name="user_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function registration(Swift_Mailer $mailer, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = new Utilisateur();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($user->getEmail());
            $user->setPassword($encoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $user->setIsEmailVerified(false);
            $user->setRole("Admin");
            try {
                $user->setToken(bin2hex(random_bytes(64)));
            } catch (Exception $e) {
                $user->setToken(uniqid("", true));
            }
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D'));
            $user->setTokenExpiresAt($expirationDate);
            try {
                $user->setRefreshToken(bin2hex(random_bytes(64)));
            } catch (Exception $e) {
                $user->setRefreshToken(uniqid("", true));
            }
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D'));
            $user->setRefreshTokenExpiresAt($expirationDate);

            $manager->persist($user);
            $manager->flush();

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

        return $this->render('auth/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login",name="auth_connexion", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion",name="auth_deconnexion", methods={"GET","POST"})
     */
    public function deconnexion()
    {
    }

    /**
     * @Route("/reinitialisation_mot_de_passe",name="auth_resetpassword", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function reset_password(Request $request, Swift_Mailer $mailer)
    {
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'reset-password rounded',
                    'placeholder' => "Adresse e-mail"
                ],
                'label' => ' '
            ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $user = $repository->findOneBy(['email' => $email]);

            if (!$user) {
                $error = "Email non existant";
                return $this->render('auth/resetpassword_admin.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }
            try {
                $user->setRefreshToken(bin2hex(random_bytes(64)));
            } catch (Exception $e) {
                # if it was not possible to gather sufficient entropy.
                $user->setRefreshToken(uniqid("", true));
            }
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D'));
            $user->setRefreshTokenExpiresAt($expirationDate);
            $manager->flush();

            $message = (new Swift_Message("Confirmer le changement de mot de passe."))
                ->setFrom('account-security-noreply@map-pym.com')
                ->setTo($user->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        "auth/email/reset_password.html.twig",
                        ['token' => $user->getRefreshToken()]
                    )
                );

            $mailer->send($message);

            return $this->redirectToRoute('auth_connexion');
        }
        $error = null;
        return $this->render('auth/resetpassword_admin.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}
