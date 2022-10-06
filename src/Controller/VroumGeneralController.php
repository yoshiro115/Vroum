<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Commande;
use App\Form\CommandeFormType;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VroumGeneralController extends AbstractController
{
    #[Route('/', name: 'app_vroum_general')]
    public function index(VehiculeRepository $repo): Response
    {
        $vehicules = $repo->findAll();
       


        return $this->render('vroum_general/index.html.twig', [
            'vehicules' => $vehicules
        ]);
    }

    #[Route('/commander/{id}', name: 'app_vroum_commander')]
    public function commander($id,VehiculeRepository $repo, Request $globals, EntityManagerInterface $manager): Response
    {
        $vehicule = $repo->find($id);
       
        $commande = new Commande;
        
        $form = $this->createForm(CommandeFormType::class, $commande );

        $form->handleRequest($globals);

        
        if($form->isSubmitted() && $form->isValid())
        { 
            $table = $globals->request->get("commande_form");
            $tableOrigin = $table["date_heure_depart"]['date'];
            $origin = $tableOrigin["year"] . "-" . $tableOrigin["month"] . "-" . $tableOrigin["day"];
            $origin = date_create($origin);
            $tableTarget = $table["date_heure_fin"]['date'];
            $target = $tableTarget["year"] . "-" . $tableTarget["month"] . "-" . $tableTarget["day"];
            $target = date_create($target);
            $commande->setDateEnregistrement(new \DateTime);
            $interval = date_diff($origin, $target);;
            $prix = $vehicule->getPrixJournalier();
            // $interval = $interval->format('d');
            $interval = ($interval->d) + ($interval->m) *30 + ($interval->y) *364 ;
            $prix = $prix * $interval;
            $commande->setPrixTotal($prix);
            $commande->setIdVehicule($vehicule);
            $commande->setIdMembre($this->getUser());
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', "votre commande a bien été accepté");
            return $this->redirectToRoute('app_vroum_general', [
                'id' => $vehicule->getId()
            ]);
        }






        return $this->renderForm('vroum_general/show.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form
        ]);

    }

    #[Route('/profil/{id}' , name:"app_profil" )]
    public function profil(CommandeRepository $repo)
    {
        $commandes = $repo->findAll();


        return $this->render('vroum_general/profil.html.twig', [
            'commandes' => $commandes
        ]);
    }
}
