<?php


namespace App\Controller;


use App\Service\DeviceNotifier\DeviceNotifierInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationBuilderController extends AbstractController
{
    private $notifier;

    public function __construct(
        DeviceNotifierInterface $notifier
    )
    {
        $this->notifier = $notifier;
    }

    /**
     * @Route("/notification_new", name="notification_new", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('title', TextType::class, ['label' => 'Titre de la notification'])
            ->add('body', TextareaType::class, ['label' => 'Corps de la notification'])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer une notification'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->notifier->notify($data['title'], $data['body']);

            return $this->render('notification/new.html.twig', [
                'form' => $form->createView(),
                'success' => true,
            ]);
        }

        return $this->render('notification/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}