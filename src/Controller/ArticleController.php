<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;

final class ArticleController extends AbstractController
{
    #[Route('/article/nouveau', name: 'app_article_nouveau')]
     public function nouveau(Request $request, EntityManagerInterface $em): Response
{
    $article = new Article();
    
    // Création du formulaire
    $form = $this->createForm(ArticleType::class, $article);
    
    // Traitement de la requête
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($article);
        $em->flush();
        
        // Message flash de confirmation
        $this->addFlash('success', 'Article créé avec succès !');
        
        return $this->redirectToRoute('app_articles');
    }
    
    return $this->render('article/nouveau.html.twig', [
        'formulaire' => $form,
    ]);
}
    #[Route('/article', name: 'app_articles')]
    public function index(ArticleRepository $articleRepository): Response
    {
    $articles = $articleRepository->findAll();

    return $this->render('article/index.html.twig', [
        'articles' => $articles,
    ]);
    }
    #[Route('/article/{id}', name: 'app_article_detail', requirements: ['id' => '\d+'])]
    public function detail(Article $article): Response
    {
    return $this->render('article/detail.html.twig', [
        'article' => $article,
    ]);
    }
    #[Route('/article/{id}/modifier', name: 'app_article_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Article $article, Request $request, EntityManagerInterface $em): Response
    {
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush(); // Pas besoin de persist() car l'entité est déjà gérée par Doctrine

        $this->addFlash('success', 'Article modifié avec succès !');
        return $this->redirectToRoute('app_article_detail', ['id' => $article->getId()]);
    }

    return $this->render('article/modifier.html.twig', [
        'formulaire' => $form,
        'article' => $article,
    ]);
    }
    #[Route('/article/{id}/supprimer', name: 'app_article_supprimer', methods: ['POST'])]
public function supprimer(Article $article, Request $request, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('supprimer_' . $article->getId(), $request->request->get('_token'))) {
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'Article supprimé avec succès.');
    }else {
        $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
    }

    return $this->redirectToRoute('app_articles');
}
}