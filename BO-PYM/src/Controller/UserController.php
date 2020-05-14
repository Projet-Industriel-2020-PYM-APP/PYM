<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationType;
use App\Form\UserEditType;
use App\Repository\UtilisateurRepository;
use DateInterval;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $utilsateurRepository;
    private $mailer;
    private $encoder;

    public function __construct(
        UtilisateurRepository $utilsateurRepository,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Swift_Mailer $mailer
    ) {
        $this->utilsateurRepository = $utilsateurRepository;
        $this->encoder = $userPasswordEncoder;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index()
    {
        return $this->render("user/index.html.twig", [
            'users' => $this->utilsateurRepository->findAll()
        ]);
    }


    /**
     * @Route("/users/me/edit",name="user_edit", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $password = $user->getPassword();
            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $message = (new Swift_Message('Modification de votre compte'))
                ->setFrom('account-security-noreply@map-pym.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        "auth/email/resetpassword_admin.html.twig",
                        ['password' => $password]
                    )
                );
            $this->mailer->send($message);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('root');
        }


        return $this->render("user/edit.html.twig", [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/edit",name="user_edit_other", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Utilisateur $utilisateur
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit_other(Utilisateur $utilisateur, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(UserEditType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $utilisateur->getPassword();
            $hash = $this->encoder->encodePassWord($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hash);

            $message = (new Swift_Message('Modification de votre compte'))
                ->setFrom('account-security-noreply@map-pym.com')
                ->setTo($utilisateur->getEmail())
                ->setBody(
                    $this->renderView(
                        "auth/email/resetpassword_admin.html.twig",
                        ['password' => $password]
                    )
                );

            $manager->flush();

            $this->mailer->send($message);

            return $this->redirectToRoute('users');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $utilisateur,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/delete",name="user_delete", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Utilisateur $utilisateur
     * @return RedirectResponse
     */
    public function delete(Utilisateur $utilisateur)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($utilisateur);
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * Response
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

        return $this->redirectToRoute('users');
    }


    /**
     * @Route("/api/users/me", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function get_user_me()
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $data = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'token' => $user->getToken(),
            'token_expires_at' => $user->getTokenExpiresAt()->format(DateTime::ISO8601),
            'role' => $user->getRole(),
            'is_email_verified' => $user->getIsEmailVerified(),
        ];
        return new JsonResponse($data);
    }
}
