<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthAPIController extends AbstractController
{
    /**
     * Returns a token by triggering the LoginApiAuthenticator.
     *
     * @Route("/api/auth/login", name="auth_api_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return Response::create(
            "Please send a HTTP POST Request with in application/x-www-form-urlencoded with post-data email=[email]&password=[password]",
            Response::HTTP_BAD_REQUEST,
            ['content-type' => 'text/html']
        );
    }

    /**
     * Create a user base on the HTTP Post data.
     *
     * It also send an email.
     *
     * @Route("/api/auth/register", name="auth_api_register", methods={"POST"})
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Swift_Mailer $mailer, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = new Utilisateur();
        $user->setEmail($request->request->get('email'));
        $user->setUsername($user->getEmail());
        $user->setPassword($encoder->encodePassword(
            $user,
            $request->request->get('password')
        ));
        $user->setIsEmailVerified(false);
        $user->setRole('User');
        try {
            $user->setToken(bin2hex(random_bytes(64)));
        } catch (\Exception $e) {
            # if it was not possible to gather sufficient entropy.
            $user->setToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1D'));
        $user->setTokenExpiresAt($expirationDate);

        $em->persist($user);
        $em->flush();

        $message = (new Swift_Message('Confirmez votre adresse e-mail.'))
            ->setFrom('account-security-noreply@map-pym.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    "auth/email/confirm_email.html.twig",
                    ['confirm_email_url' => '']  # TODO: Here
                )
            );

        $mailer->send($message);

        return Response::create(
            $user->getToken(),
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * Send an email to confirm the email address.
     *
     * The method accept a Bearer Token generated with /api/auth/register or /api/auth/login.
     * It reset the email verification state to false.
     * It generate an url and send it to the owner of the email address.
     *
     * @Route("/api/auth/email_verification", name="auth_api_email_verification", methods={"POST"})
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UtilisateurRepository $repository
     * @return Response
     */
    public function emailVerification(Swift_Mailer $mailer, Request $request, EntityManagerInterface $em, UtilisateurRepository $repository, UrlGeneratorInterface $urlGenerator)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        if (is_null($authorizationHeader)) {
            throw $this->createAccessDeniedException();
        }
        $token = substr($authorizationHeader, 7);
        $user = $repository->findOneBy(["token"=> $token]);
        $user->setIsEmailVerified(false);
        try {
            $user->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (\Exception $e) {
            # if it was not possible to gather sufficient entropy.
            $user->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1H'));
        $user->setRefreshTokenExpiresAt($expirationDate);

        $confirmationLink = $urlGenerator->generate(
            'auth_confirm_email',
            ['token' => $user->getRefreshToken()]
        );

        $message = (new Swift_Message('Confirmez votre adresse e-mail.'))
            ->setFrom('account-security-noreply@map-pym.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    "auth/email/confirm_email.html.twig",
                    ['confirm_email_url' => $confirmationLink]
                )
            );

        $mailer->send($message);

        $em->flush();

        return Response::create(
            "Sent",
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/api/auth/forgot_password", name="auth_api_email_verification", methods={"POST"})
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param UtilisateurRepository $repository
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function forgotPassword(Swift_Mailer $mailer, Request $request, UtilisateurRepository $repository, UrlGeneratorInterface $urlGenerator)
    {
        $email = $request->headers->get('email');
        $user = $repository->findOneBy(["email"=> $email]);
        if (is_null($user)) {
            throw $this->createNotFoundException(
                'Email not found' / $email
            );
        }
        try {
            $user->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (\Exception $e) {
            # if it was not possible to gather sufficient entropy.
            $user->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1H'));
        $user->setRefreshTokenExpiresAt($expirationDate);

        $confirmationLink = $urlGenerator->generate(
            'auth_reset_password',
            ['token' => $user->getRefreshToken()]
        );
        $message = (new Swift_Message("Confirmer le changement d'addresse email."))
            ->setFrom('account-security-noreply@map-pym.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    "auth/email/reset_password.html.twig",
                    ['confirm_email_url' => $confirmationLink]
                )
            );

        $mailer->send($message);

        return Response::create(
            "Sent",
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/auth/confirm_email/{token}", name="auth_confirm_email", methods={"GET"})
     * @param string $token
     * @param EntityManagerInterface $em
     * @param UtilisateurRepository $repository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function confirmEmail(string $token, EntityManagerInterface $em, UtilisateurRepository $repository, UserPasswordEncoderInterface $encoder)
    {
        $user = $repository->findOneBy(["refreshToken"=> $token]);
        if (is_null($user)) {
            throw $this->createNotFoundException('User not found');
        }
        if ($user->isRefreshTokenExpired()) {
            throw $this->createAccessDeniedException('Link has expired.');
        }
        $user->setRefreshTokenExpiresAt(new DateTime());
        $user->setRefreshToken(null);
        $user->setIsEmailVerified(true);
        $em->flush();
        return $this->render("auth/confirmed_email.html.twig");
    }

    /**
     * @Route("/auth/reset_password/{token}", name="auth_reset_password", methods={"GET", "POST"})
     * @param string $token
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UtilisateurRepository $repository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetPassword(string $token, Request $request, EntityManagerInterface $em, UtilisateurRepository $repository, UserPasswordEncoderInterface $encoder)
    {
        $user = $repository->findOneBy(["refreshToken"=> $token]);
        if (is_null($user)) {
            throw $this->createNotFoundException('User not found');
        }
        if ($user->isRefreshTokenExpired()) {
            throw $this->createAccessDeniedException('Link has expired.');
        }

        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label_attr' => [
                    'class' => 'h5'
                ],
                'attr' => [
                    'placeholder' => "Mot de passe",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "Mot de passe",
            ])
            ->add('confirm_password', PasswordType::class, [
                'row_attr' => [
                    'class' => 'form-group'
                ],
                'label_attr' => [
                    'class' => 'h5'
                ],
                'attr' => [
                    'placeholder' => "Mot de passe",
                    'class' => 'reg rounded form-control'
                ],
                'label' => "Mot de passe",
            ])
            ->add('_submit', SubmitType::class, [
                'label' => 'Changer de mot de passe'
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRefreshTokenExpiresAt(new DateTime());
            $user->setRefreshToken(null);
            $em->flush();

            return $this->render(
                "auth/confirmed_reset_password.html.twig"
            );
        }


        return $this->render(
            "auth/reset_password.html.twig",
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/api/auth/logout", name="auth_api_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
