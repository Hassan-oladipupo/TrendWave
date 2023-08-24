<?php
//The namespace of the src folder, This must be include in all  code in src folder
namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\UserProfile;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//Defining the controller class
class HelloController extends AbstractController
{
    private array $message = [
        ['message' => 'Hello', 'created' => '2023/03/12'],
        ['message' => 'Hi', 'created' => '2023/01/12'],
        ['message' => 'Bye!', 'created' => '2022/05/12']
    ];

    //defining the routes for this action
    #[Route('/',   name: 'app_index')]
    public function index(MicroPostRepository $posts, CommentRepository $comments): Response
    {
        //Creating a post and adding a comment
        // $post = new MicroPost();
        // $post->setTitle('Hello');
        // $post->setText('Hello');
        // $post->setCreatedate(new DateTime());

        // $comment = new Comment();
        // $comment->setText('Hello');
        // $post->addComment($comment);     
        // $comment->setPost($post);
        // $posts->save($post, true);


        //Removing the comment
        // $post = $posts->find(14);
        // $comment = $post->getComments()[0];
        // $comment->setPost($post);
        // $post->removeComment($comment);
        // $comments->save($comment, true);

        //dd($post);


        //Creating a user 
        // $user = new User();
        // $user->setEmail('email@email.com');
        // $user->setPassword('12345678');

        //Adding profiles
        // $profile = new UserProfile();
        // $profile->setUser($user);
        // $profiles->save($profile, true);

        // $profile = $profiles->find(1);
        // $profiles->remove($profile, true);


        return  $this->render(
            'HelloView/index.html.twig',
            [
                'messages' => $this->message,
                'limit' => 3
            ]
        );
    }

    //defining the routes for this action
    #[Route('/message/{id<\d+>}',   name: 'app_Show_One')]
    public function ShowOne(int $id): Response
    {
        //how to return a template or a view
        return $this->render(
            'HelloView/show_one.html.twig',
            [
                'message' => $this->message[$id]
            ]
        );
        // return new Response($this->message[$id]);
    }
}
