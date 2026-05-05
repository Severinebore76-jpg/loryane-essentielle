<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\ProduitRepository;
use App\Repository\AdresseRepository;
use App\Entity\Utilisateur;
use App\Service\PanierService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/recap', name: 'commande_recap')]
    public function recap(
        PanierService $panierService,
        ProduitRepository $produitRepository
    ): Response {
        $panier = $panierService->getPanierComplet($produitRepository);
        $total = $panierService->getTotal($produitRepository);
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }
        $adresses = $user->getAdresses();

        if (empty($panier)) {
            $this->addFlash('error', 'Panier vide');
            return $this->redirectToRoute('app_panier');
        }

        return $this->render('commande/recap.html.twig', [
            'panier' => $panier,
            'total' => $total,
            'adresses' => $adresses,
        ]);
    }

    #[Route('/commande/create', name: 'commande_create', methods: ['POST'])]
    public function createCommande(
        Request $request,
        PanierService $panierService,
        ProduitRepository $produitRepository,
        AdresseRepository $adresseRepository,
        EntityManagerInterface $em
    ): Response {

        // IMPORTANT : on utilise getPanierComplet
        $panier = $panierService->getPanierComplet($produitRepository);

        if (empty($panier)) {
            $this->addFlash('error', 'Panier vide');
            return $this->redirectToRoute('app_panier');
        }

        // =========================
        // VALIDATION
        // =========================
        foreach ($panier as $item) {

            $produit = $item['produit'];
            $quantite = $item['quantite'];

            if ($quantite <= 0) {
                $this->addFlash('error', 'Quantité invalide');
                return $this->redirectToRoute('app_panier');
            }

            if (!$produit) {
                $this->addFlash('error', 'Produit introuvable');
                return $this->redirectToRoute('app_panier');
            }

            if ($produit->getStock() < $quantite) {
                $this->addFlash('error', 'Stock insuffisant pour ' . $produit->getNom());
                return $this->redirectToRoute('commande_recap');
            }
        }

        // =========================
        // ADRESSE
        // =========================
        $adresseId = $request->request->get('adresse_id');
        $adresse = $adresseRepository->find($adresseId);

        if (!$adresse) {
            $this->addFlash('error', 'Adresse invalide');
            return $this->redirectToRoute('commande_recap');
        }

        // =========================
        // COMMANDE
        // =========================
        $commande = new Commande();
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException();
        }
        $commande->setUtilisateur($user);
        $commande->setAdresse($adresse);
        $commande->setDate(new DateTimeImmutable());
        $commande->setStatut('pending');
        $commande->setTotal(
            $panierService->getTotal($produitRepository)
        );
        $em->persist($commande);

        // =========================
        // LIGNES DE COMMANDE
        // =========================
        foreach ($panier as $item) {

            $produit = $item['produit'];
            $quantite = $item['quantite']; //

            $ligne = new LigneCommande();

            $ligne->setProduit($produit);
            $ligne->setQuantite($quantite);
            $ligne->setPrix($produit->getPrix());
            $ligne->setCommande($commande);

            $em->persist($ligne);
        }
        // =========================
        // FINALISATION
        // =========================
        $em->flush();

        $panierService->clear();

        $this->addFlash('order_success', 'Commande validée avec succès');

        return $this->redirectToRoute('commande_detail', [
            'id' => $commande->getId()
        ]);
    }
    #[Route('/mes-commandes', name: 'commande_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $commandes = $em->getRepository(Commande::class)
            ->findBy(
                ['utilisateur' => $this->getUser()],
                ['date' => 'DESC']
            );

        return $this->render('commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/{id}', name: 'commande_detail')]
    public function detail(Commande $commande): Response
    {
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }
}
