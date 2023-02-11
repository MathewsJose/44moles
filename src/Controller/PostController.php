<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\PostRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();        
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts            
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(ManagerRegistry $doctrine, Request $request): Response {

        $post = new Post();
        $post->setTitle('');
        $post->setDescription('');

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);        
        if($form->isSubmitted()){
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl(route: 'home.index'));
        }
        return $this->render('post/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")     
     * @return Response
     */
    public function show($id, PostRepository $postRepository){
        $post = $postRepository->find($id);        
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
