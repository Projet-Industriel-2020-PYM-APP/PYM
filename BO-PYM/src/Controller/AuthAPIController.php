<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationAPIType;
use App\Form\ResetPasswordType;
use App\Repository\UtilisateurRepository;
use DateInterval;
use DateTime;
use Exception;
use Laminas\Code\Scanner\Util;
use LogicException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class AuthAPIController extends AbstractController
{
    private $logger;
    private $mailer;
    private $repository;

    public function __construct(
        LoggerInterface $logger,
        Swift_Mailer $mailer,
        UtilisateurRepository $repository
    ) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->repository = $repository;
    }

    /**
     * Returns a token by triggering the LoginApiAuthenticator.
     *
     * @Route("/api/auth/login", name="auth_api_login", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $this->logger->alert($error);

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
     * @Route("/api/auth/register", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $encoder
    ) {
        $em = $this->getDoctrine()->getManager();
        // Fill data
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationAPIType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($user->getEmail());
            $user->setPassword($encoder->encodePassword(
                $user,
                $user->getPassword()
            ));
            $user->setIsEmailVerified(false);
            $user->setRole('User');
            try {
                $user->setToken(bin2hex(random_bytes(64)));
            } catch (Exception $e) {
                $this->logger->warning($e);
                $user->setToken(uniqid("", true));
            }
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D'));
            $user->setTokenExpiresAt($expirationDate);
            try {
                $user->setRefreshToken(bin2hex(random_bytes(64)));
            } catch (Exception $e) {
                $this->logger->warning($e);
                $user->setRefreshToken(uniqid("", true));
            }
            $expirationDate = new DateTime();
            $expirationDate->add(new DateInterval('P1D'));
            $user->setRefreshTokenExpiresAt($expirationDate);

            $em->persist($user);
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

            $this->mailer->send($message);

            return Response::create(
                $user->getToken(),
                Response::HTTP_OK
            );
        }
        return Response::create(
            $form->getErrors(true),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Send an email to confirm the email address.
     *
     * The method accept a Bearer Token generated with /api/auth/register or /api/auth/login.
     * It reset the email verification state to false.
     * It generate an url and send it to the owner of the email address.
     *
     * @Route("/api/auth/email_verification", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @return Response
     */
    public function emailVerification()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $user->setIsEmailVerified(false);
        try {
            $user->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (Exception $e) {
            $this->logger->warning($e);
            $user->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1D'));
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

        $this->mailer->send($message);

        return Response::create(
            "Sent",
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/auth/forgot_password", name="auth_forgot_password", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param Request $request
     * @return Response
     */
    public function forgotPassword(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->request->get('email');
        $user = $this->repository->findOneBy(["email" => $email]);
        if (is_null($user)) {
            throw $this->createNotFoundException(
                'Email not found : ' . $email
            );
        }
        try {
            $user->setRefreshToken(bin2hex(random_bytes(64)));
        } catch (Exception $e) {
            $this->logger->warning($e);
            $user->setRefreshToken(uniqid("", true));
        }
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('P1D'));
        $user->setRefreshTokenExpiresAt($expirationDate);
        $em->flush();

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

        $this->mailer->send($message);

        return Response::create(
            "Sent",
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    }

    /**
     * @Route("/auth/confirm_email/{token}", name="auth_confirm_email", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param string $token
     * @return Response
     */
    public function confirmEmail(string $token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->repository->findOneBy(["refreshToken" => $token]);
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
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param string $token
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetPassword(
        string $token,
        Request $request,
        UserPasswordEncoderInterface $encoder
    ) {
        $user = $this->repository->findOneBy(["refreshToken" => $token]);
        if (is_null($user)) {
            throw $this->createNotFoundException('User not found.');
        }
        if ($user->isRefreshTokenExpired()) {
            throw $this->createAccessDeniedException('Link has expired.');
        }
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRefreshTokenExpiresAt(new DateTime());
            $user->setRefreshToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $message = (new Swift_Message("Mot de passe modifié."))
                ->setFrom('account-security-noreply@map-pym.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        "auth/email/confirmed_email.html.twig"
                    )
                );

            $this->mailer->send($message);

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
     * @Route("/api/auth/logout", name="auth_api_logout", methods={"GET", "POST"})
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
