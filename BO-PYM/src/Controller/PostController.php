<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\DeviceNotifier\DeviceNotifierInterface;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

header("Access-Control-Allow-Origin: *");

class PostController extends AbstractController
{
    private $postRepository;
    private $notifier;

    public function __construct(
        PostRepository $postRepository,
        DeviceNotifierInterface $notifier
    )
    {
        $this->postRepository = $postRepository;
        $this->notifier = $notifier;
    }

    /**
     * @Route("/post", name="post_index", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $this->postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/post/new", name="post_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $post->setPublished(new DateTime());
        $post->setUpdated(new DateTime());
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUrl($request->getScheme() . '://' . $request->getHttpHost() . "/post/" . $post->getId());  // TODO: Add here Front-End url
            $entityManager->persist($post);
            $entityManager->flush();

            $this->notifier->notifyPost($post, 'actualite');

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Post $post
     * @return Response
     */
    public function show(Post $post)
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/{id}/edit", name="post_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdated(new DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}/delete", name="post_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Post $post
     * @return Response
     */
    public function delete(Post $post): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/api/posts", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function fetchAllAction(): Response
    {
        $posts = $this->postRepository->findAll();
        return new JsonResponse($posts);
    }
}
