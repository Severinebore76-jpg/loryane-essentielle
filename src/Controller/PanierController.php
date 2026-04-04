<?php

namespace App\Controller;

use App\Service\PanierService;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    // + AJOUTER QUANTITÉ
    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function add(int $id, Request $request, PanierService $panierService): Response
    {
        $panierService->add($id);

        return $this->handleRedirect($request);
    }

    // ➖ DIMINUER QUANTITÉ
    #[Route('/panier/decrease/{id}', name: 'panier_decrease')]
    public function decrease(int $id, Request $request, PanierService $panierService): Response
    {
        $panierService->decrease($id);

        return $this->handleRedirect($request);
    }

    // ❌ SUPPRIMER PRODUIT
    #[Route('/panier/remove/{id}', name: 'panier_remove')]
    public function remove(int $id, Request $request, PanierService $panierService): Response
    {
        $panierService->remove($id);

        return $this->handleRedirect($request);
    }

    // 🔁 GESTION REDIRECTION INTELLIGENTE
    private function handleRedirect(Request $request): Response
    {
        if ($request->query->get('redirect') === 'panier') {
            return $this->redirectToRoute('app_panier');
        }

        return $this->redirectToRoute('app_produits');
    }

    // VIDER PANIER
    #[Route('/panier/clear', name: 'panier_clear')]
    public function clear(PanierService $panierService): Response
    {
        $panierService->clear();

        return $this->redirectToRoute('app_panier');
    }

    // VALIDER PANIER
    #[Route('/panier/valider', name: 'panier_valider')]
    public function valider(PanierService $panierService): Response
    {
        $panier = $panierService->getPanier();

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_panier');
        }

        return $this->redirectToRoute('commande_recap');
    }
}
