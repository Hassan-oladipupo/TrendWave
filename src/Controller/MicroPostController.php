<?php

namespace App\Controller;

use App\Entity\Comment;
use DateTime;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use PhpParser\Node\Stmt\Label;
use App\Repository\MicroPostRepository;
use ContainerPTnSoI9\getForm_FactoryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use ContainerPTnSoI9\getForm_ChoiceListFactory_CachedService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render(
            'micro_post/index.html.twig',
            [
                //Using findAll method to get post from the DB
                'posts' => $posts->findAllWithComments(),
            ]
        );
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {
        return $this->render(
            'micro_post/top_liked.html.twig',
            [
                'posts' => $posts->findAllWithMinLikes(2),
            ]
        );
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render(
            'micro_post/follows.html.twig',
            [
                'posts' => $posts->findAllByAuthors(
                    $currentUser->getFollows()
                ),
            ]
        );
    }




    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {
        return $this->render(
            'micro_post/show.html.twig',
            [
                'post' => $post,
            ]
        );
    }

    //Method for adding forms for the micropost
    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('ROLE_WRITER')]
    public function add(Request $request, MicroPostRepository $posts): Response
    {
        // $this->denyAccessUnlessGranted(
        //     'IS_AUTHENTICATED_FULLY'
        // );



        //Form class creation 
        $form = $this->createForm(MicroPostType::class, new MicroPost());


        //To submit the form
        $form->handleRequest($request);


        //Verifying if the form was submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            //getting the data of the from post
            $post = $form->getData();


            //setting the post to the author who is making the post
            $post->setAuthor($this->getUser());

            //Using the  MicroPost repository to store the data
            $posts->save($post, true);
            //add flash message
            $this->addFlash('success', 'Your Micropost as been added');
            //redirecting to all post
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->renderForm(
            '/micro_post/add.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    //Method for Edit forms for the MicroPost
    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post,  Request $request, MicroPostRepository $posts): Response
    {

        $form = $this->createForm(MicroPostType::class, $post);



        //To submit the form
        $form->handleRequest($request);

        //Verifying if the form was submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            //getting the data of the from post
            $post = $form->getData();

            //Using the MicroPost repository to store the data
            $posts->save($post, true);
            //add flash message
            $this->addFlash('success', 'Your Micropost as been updated');
            //redirecting to all post
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->renderForm(
            '/micro_post/edit.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }


    //Method for adding comment forms for the MicroPost
    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]

    public function addComment(MicroPost $post,  Request $request,  CommentRepository $comments): Response
    {

        $form = $this->createForm(CommentType::class, new Comment());



        //To submit the form
        $form->handleRequest($request);

        //Verifying if the form was submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {

            //getting the data of the comment
            $comment = $form->getData();


            //Associating  the comment to a Post by 
            $comment->setPost($post);


            //setting the comment to the author who is making the comment
            $comment->setAuthor($this->getUser());

            //Using the comment repository to store the data
            $comments->save($comment, true);
            //add flash message
            $this->addFlash('success', 'Your Comment as been added');
            //redirecting to the post
            return $this->redirectToRoute('app_micro_post_show', ['post' => $post->getId()]);
        }

        return $this->renderForm(
            '/micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post

            ]
        );
    }
}
