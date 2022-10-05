<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Vehicule;
use App\Form\MembreType;
use App\Form\VehiculeType;
use App\Repository\MembreRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/vehicule/edit/{id}', name:'admin_edit_vehicule')]
    #[Route('/admin/vehicules', name: "admin_vehicules")]
    public function adminVehicule(Request $globals, VehiculeRepository $repo, EntityManagerInterface $manager, Vehicule $vehicule = null)
    {
        $colonnes =$manager->getClassMetadata(Vehicule::class)->getFieldNames();

        // dd($colonnes);
        $vehicules = $repo->findAll();
        

        if($vehicule ==null):
            $vehicule = new Vehicule;
        endif;


        $form= $this->createForm(VehiculeType::class, $vehicule );

        $form->handleRequest($globals);



        //dump($vehicule);

        if($form->isSubmitted() && $form->isValid())
        {
            $vehicule->setDateEnregistrement(new \DateTime);
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', "L'vehicule a bien été enregistré !");
            //permet de creer un message qui sera affiché une fois a l'utilisateur
            return $this->redirectToRoute('admin_vehicules');
        }

        return $this->renderForm("admin/admin_vehicules.html.twig", [
            "formVehicule" => $form,
            "editMode" => $vehicule->getId() !== null,
            'vehicules' => $vehicules,
            'colonnes' => $colonnes
        ]);

    }
    #[Route("/admin/vehicule/delete/{id}", name:"admin_delete_vehicule")]
    public function deleteVehicule(Vehicule $vehicule, EntityManagerInterface $manager)
    {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('success', "le vehicule a bien été supprimé !");
        return $this->redirectToRoute('admin_vehicules');

    }
}
