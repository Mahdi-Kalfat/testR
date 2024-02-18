<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnectionType;
use App\Form\CreecompteType;
use App\Form\EditProfilImgType;
use App\Form\EditProfilType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/singin', name: 'app_user_singin')]
    public function creecompte(EntityManagerInterface $em,Request $req)
    {
       $user = new User();
       $form=$this->createForm(CreecompteType::class,$user);
       $defaultImage = 'Default.jpg';
        $form->get('image')->setData($defaultImage);
        
       $form->handleRequest($req);
       if ($form->isSubmitted()){
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('app_user_login');
       }
       return $this->render('user/creecompte.html.twig', ['formAdd'=>$form]);
    }
    #[Route('/user/home', name: 'app_user_home')]
    public function index4(): Response
    {
        return $this->render('user/home.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/profil', name: 'app_user_profil')]
    public function index5(): Response
    {
        return $this->render('user/profil.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/login', name: 'app_user_login')]
    public function login(UserRepository $userRepository, Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(ConnectionType::class);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            // Access individual form fields
            $mail = $formData->getMail();
            $mdp = $formData->getMdp();
    
            // Find user by email and password
            $user = $userRepository->findUserByEmailAndPassword($mail, $mdp);
    
            if ($user) {
                $stat = $user->getStat();
    
                if ($stat !== "bani") {
                    $session->set('user_email', $mail);
                    return $this->redirectToRoute('app_user_home');
                } else {
                    $this->addFlash('error', 'Désolée votre compte est banni !');
                }
            } else {
                $this->addFlash('error', 'Email or password is incorrect.');
            }
        }
        return $this->render('user/connect.html.twig', [
            'form' => $form->createView(),
        ]);
    }
  
    #[Route('/user/profil', name: 'app_user_profil')]
public function profile(SessionInterface $session, UserRepository $userRepository,EntityManagerInterface $em,Request $req): Response
{
    // Retrieve the email from the session
    $email = $session->get('user_email');
    $user = $userRepository->findOneBy(['mail' => $email]);
    $nom = $user->getNom();
    $prenom = $user->getPrenom();
    $stat = $user->getStat();
    $numero = $user->getNum();
    $pass = $user-> getMdp();
    $adress = $user->getAdresse();
    $id = $user->getId();
    $image = $user->getImage();

    $user = $userRepository->find($id);

    $form=$this->createForm(EditProfilImgType::class,$user);
       $form->handleRequest($req);
       if ($form->isSubmitted()){
        $imageFile = $form->get('image')->getData();
       /* if ($imageFile) {*/
            $newFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move(
                'C:\Users\mehdi\OneDrive\Desktop\projet_mahdi\projet_wetek\public\imguser', // Répertoire de stockage des images
                $newFileName 
            );
       // }*/
            $user->setImage($newFileName);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('app_user_profil');
    }


    return $this->render('user/profil.html.twig', [ 'formadd' => $form,
        'email' => $email,
        'nom' => $nom,
        'prenom'=>$prenom, 
        'status'=>$stat,
        'num' => $numero,
        'mdp' => $pass,
        'adresse' => $adress,
        'id' => $id,
        'img' => $image
    ]);
}

#[Route('/user/profil/edit/{id}', name: 'app_user_profil_edit')]
public function profileedit($id,EntityManagerInterface $em,SessionInterface $session, UserRepository $userRepository,Request $req): Response
{
    // Retrieve the email from the session
    $email = $session->get('user_email');
    $user = $userRepository->findOneBy(['mail' => $email]);
    $nom = $user->getNom();
    $prenom = $user->getPrenom();
    $stat = $user->getStat();
    $img = $user->getImage();
    
    $user = $userRepository->find($id);
       $form=$this->createForm(EditProfilType::class,$user);
       $form->handleRequest($req);
       if ($form->isSubmitted()){
        $user->setImage($user->getImage());
        $em->persist($user);
        $em->flush();
       
        return $this->redirectToRoute('app_user_profil');
    }

    return $this->render('user/editprofil.html.twig', ['formedit'=>$form,
        'email' => $email,
        'nom' => $nom,
        'prenom'=>$prenom, 
        'status'=>$stat,
        'img' => $img

    ]);
}
}
