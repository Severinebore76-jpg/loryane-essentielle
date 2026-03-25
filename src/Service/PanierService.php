<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProduitRepository;

class PanierService
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function getPanier(): array
    {
        return $this->session->get('panier', []);
    }

    public function add(int $produitId): void
    {
        $panier = $this->getPanier();

        if (!empty($panier[$produitId])) {
            $panier[$produitId]++;
        } else {
            $panier[$produitId] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function remove(int $produitId): void
    {
        $panier = $this->getPanier();

        if (isset($panier[$produitId])) {
            unset($panier[$produitId]);
        }

        $this->session->set('panier', $panier);
    }

    public function update(int $produitId, int $quantite): void
    {
        $panier = $this->getPanier();

        if ($quantite <= 0) {
            unset($panier[$produitId]);
        } else {
            $panier[$produitId] = $quantite;
        }

        $this->session->set('panier', $panier);
    }

    public function clear(): void
    {
        $this->session->remove('panier');
    }

    // 🔥 TOTAL PANIER
    public function getTotal(ProduitRepository $produitRepository): float
    {
        $panier = $this->getPanier();
        $total = 0;

        foreach ($panier as $produitId => $quantite) {

            $produit = $produitRepository->find($produitId);

            if ($produit) {
                $total += $produit->getPrix() * $quantite;
            }
        }

        return $total;
    }

    // 🔥 PANIER COMPLET (COHÉRENCE + PRÉPARATION AFFICHAGE)
    public function getPanierComplet(ProduitRepository $produitRepository): array
    {
        $panier = $this->getPanier();
        $panierComplet = [];

        foreach ($panier as $produitId => $quantite) {

            $produit = $produitRepository->find($produitId);

            if (!$produit) {
                continue;
            }

            if ($quantite <= 0) {
                continue;
            }

            if ($produit->getStock() < $quantite) {
                $quantite = $produit->getStock();
            }

            $panierComplet[] = [
                'produit' => $produit,
                'quantite' => $quantite,
            ];
        }

        return $panierComplet;
    }

    // 🔥 PRÉPARATION DONNÉES COMMANDE
    public function getPanierPourCommande(ProduitRepository $produitRepository): array
    {
        $panierComplet = $this->getPanierComplet($produitRepository);

        $commandeData = [];

        foreach ($panierComplet as $item) {

            $produit = $item['produit'];
            $quantite = $item['quantite'];

            $commandeData[] = [
                'produit_id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'quantite' => $quantite,
                'total_ligne' => $produit->getPrix() * $quantite,
            ];
        }

        return $commandeData;
    }
}
