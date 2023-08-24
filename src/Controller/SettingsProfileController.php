<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Form\ProfileImageType;
use App\Repository\UserRepository;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, UserRepository $users): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();


        $form = $this->createForm(
            UserProfileType::class,
            $userProfile
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userProfile = $form->getData();

            $user->setUserProfile($userProfile);
            $users->save($user, true);
            $this->addFlash(
                'success',
                'Your user profile settings were saved.'
            );
            return $this->redirectToRoute(
                'app_settings_profile'
            );
        }

        return $this->render('settings_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/settings/profile-image', name: 'app_settings_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger, UserRepository $users): Response
    {
        //Form class creation 
        $form = $this->createForm(ProfileImageType::class);


        /** @var User $user */
        //getting current user 
        $user = $this->getUser();

        //handling the request
        $form->handleRequest($request);

        //checking if form is Submit and valid
        if ($form->isSubmitted() && $form->isValid()) {

            //getting the file object that was submit
            $profileImageFile = $form->get('profileImage')->getData();

            //checking if file object is not null
            if ($profileImageFile) {

                //getting original file name
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                //generating our own new name for the file
                $safeFilename = $slugger->slug($originalFileName);
                $newFileName = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();


                //moving the file to their destination directory
                try {
                    $profileImageFile->move(

                        //getting the parameter of the file directory we define in the service.yaml file
                        $this->getParameter('profiles_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                }

                //getting a user profile that already exist or getting a new user profile for a user that has no user profile before

                $profile = $user->getUserProfile() ?? new UserProfile();
                //uploading the image 
                $profile->setImage($newFileName);

                //setting to the user profile to the image he upload
                $user->setUserProfile($profile);

                //saving the changes
                $users->save($user, true);

                // Adding a flash message
                $this->addFlash('success', 'Your profile image was updated.');

                //redirecting to profile image template
                return $this->redirectToRoute('app_settings_profile_image');
            }
        }

        return $this->render(
            'settings_profile/profile_image.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
