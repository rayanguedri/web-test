<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime as ConstraintsDateTime;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(PublicationRepository $repo): Response
    {


        $publications = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'publications' => $publications
        ]);
    }

    /**
     * @Route("/", name="home")
     */

    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue dans notre blog",
            'age' => 31,
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit" , name = "blof_edit")
     */
    public function form(Publication $publication = null, Request $request , EntityManagerInterface $manager )
    {

        if(!$publication){

            $publication = new Publication;

        }
        

        $form = $this->createFormBuilder($publication)
            ->add('titre')
            ->add('contenu')
            ->add('image') 
            ->getForm();

            $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()) {
                if (!$publication->getId()) {
                    $publication->setCreatedAt (new \DateTimeImmutable());
                }
               

                $manager->persist($publication);
                $manager ->flush();

                return $this->redirectToRoute('blog_show',['id' => $publication->getId()]);


            }


        return $this->render('blog/create.html.twig',[
            'formPublication' => $form->createView(),
            'editMode' =>  $publication->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Publication $publication)
    {

        return $this->render('blog/show.html.twig', [
            'publication' => $publication
        ]);
    }
}
