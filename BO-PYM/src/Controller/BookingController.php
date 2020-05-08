<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Service;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\ServiceRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private $bookingRepository;
    private $serviceRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        ServiceRepository $serviceRepository
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @Route("/services/{id}/bookings", name="booking_of_service_index", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Service $service
     * @return Response
     */
    public function index(Service $service): Response
    {
        return $this->render('booking/calendar.html.twig', [
            'service' => $service,
        ]);
    }

    /**
     * @Route("/services/{id_srv}/bookings/add", name="booking_of_service_add", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param int $id_srv
     * @param Request $request
     * @return Response
     */
    public function add(int $id_srv, Request $request): Response
    {
        $booking = new Booking();
        $booking->setServiceId($id_srv);
        $booking->setStartDate(new DateTime());
        $booking->setEndDate(new DateTime());
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('booking_of_service_index', [
                'id' => $booking->getServiceId()
            ]);
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bookings/{id}", name="booking_of_service_show", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking): Response
    {
        $service = $this->serviceRepository->find($booking->getServiceId());
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'service' => $service
        ]);
    }

    /**
     * @Route("/bookings/{id}/edit", name="booking_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_of_service_index', [
                'id' => $booking->getServiceId(),
            ]);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bookings/{id}/delete", name="booking_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function delete(Request $request, Booking $booking): Response
    {
        $id_srv = $booking->getServiceId();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($booking);
        $entityManager->flush();

        return $this->redirectToRoute('booking_of_service_index', [
            'id' => $id_srv,
        ]);
    }

    /**
     * @Route("/api/bookings", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @return Response
     */
    public function get_bookings(): Response
    {
        $bookingsOfService = $this->bookingRepository->findAll();
        return new JsonResponse($bookingsOfService);
    }

    /**
     * @Route("/api/bookings/{id}", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @param Booking $booking
     * @return Response
     */
    public function get_booking(Booking $booking): Response
    {
        return new JsonResponse($booking);
    }

    /**
     * @Route("/api/bookings", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function post_booking(Request $request): Response
    {
        $booking = new Booking();
        $serviceId = $request->request->get('service_id');
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');
        $title = $request->request->get('title');

        if (
            is_null($serviceId)
            || is_null($startDate)
            || is_null($endDate)
            || is_null($title)
        ) {
            return Response::create('400 Bad request', Response::HTTP_BAD_REQUEST);
        }

        $booking->setServiceId($serviceId);
        $booking->setStartDate(DateTime::createFromFormat(DateTime::ISO8601, $startDate));
        $booking->setEndDate(DateTime::createFromFormat(DateTime::ISO8601, $endDate));
        $booking->setTitle($title);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        $entityManager->flush();

        return new Response(
            "Added.",
            Response::HTTP_CREATED,
            ['Location' => '/api/bookings/' . $booking->getId()]
        );
    }

    /**
     * @Route("/api/bookings/{id}", methods={"PATCH"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function patch_booking(Request $request, Booking $booking): Response
    {
        $serviceId = $request->request->get('service_id');
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');
        $title = $request->request->get('title');
        if (!is_null($serviceId)) {
            $booking->setServiceId($serviceId);
        }
        if (!is_null($startDate)) {
            $booking->setStartDate(DateTime::createFromFormat(DateTime::ISO8601, $startDate));
        }
        if (!is_null($endDate)) {
            $booking->setEndDate(DateTime::createFromFormat(DateTime::ISO8601, $endDate));
        }
        if (!is_null($title)) {
            $booking->setTitle($title);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response("Updated {merged}.");
    }


    /**
     * @Route("/api/bookings/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Booking $booking
     * @return Response
     */
    public function delete_booking(Booking $booking): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($booking);
        $em->flush();
        return new Response("Deleted.");
    }
}
