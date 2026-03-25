<?php

namespace App\Controller;

use App\Service\PanierService;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService, ProduitRepository $produitRepository): Response
    {
        $panier = $panierService->getPanierComplet($produitRepository);
        $total = $panierService->getTotal($produitRepository);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function add(int $id, PanierService $panierService): Response
    {
        $panierService->add($id);

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/panier/valider', name: 'panier_valider')]
    public function valider(PanierService $panierService): Response
    {
        $panier = $panierService->getPanier();

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');

            return $this->redirectToRoute('app_panier');
        }

        // accès autorisé → suite logique (temporaire)
        return $this->redirectToRoute('app_panier');
    }
}
