<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TacheRepository;


final class TacheController extends AbstractController
{
    #[Route('/taches', name: 'app_taches')]
    public function index(TacheRepository $repo): Response
    {
        $taches = $repo->findBy([], ['terminee' => 'ASC']);
        return $this->render('tache/index.html.twig', ['taches' => $taches]);
    }
    #[Route('/taches/ajouter', name: 'app_tache_ajouter')]
    public function ajouter(EntityManagerInterface $em): Response
    {
        $tache = new Tache();
        $tache->setTitre('Ma première tâche');
        $tache->setDescription('Ceci est une description');
        $tache->setTerminee(false);
        $tache->setDateCreation(new \DateTime());
        $em->persist($tache);
        $em->flush();
        return new Response('Tâche créée ! ID : ' . $tache->getId());
    }
    #[Route('/taches/{id}', name: 'app_tache_show')]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', ['tache' => $tache]);
    }
    #[Route('/taches/{id}/terminer', name: 'app_tache_terminer')]
    public function terminer(Tache $tache, EntityManagerInterface $em): Response
    {
    $tache->setTerminee(true);
    $em->flush();

    return $this->redirectToRoute('app_taches');
    }
}
