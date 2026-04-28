<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // 🔒 Sécurisation : accès réservé aux admins
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/index.html.twig');
    }
    #[Route('/admin/commandes', name: 'admin_commandes')]
    public function commandes(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commandes = $em->getRepository(Commande::class)
            ->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->where('u.email != :admin')
            ->setParameter('admin', 'admin@test.com')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    #[Route('/admin/commande/{id}', name: 'admin_commande_detail')]
    public function detailCommande(
        int $id,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commande = $em->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('admin/commande_detail.html.twig', [
            'commande' => $commande,
        ]);
    }
    #[Route('/admin/commande/{id}/statut', name: 'admin_commande_update_statut', methods: ['POST'])]
    public function updateStatut(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $commande = $em->getRepository(Commande::class)->find($id);

        if (!$commande) {
            throw $this->createNotFoundException();
        }

        $statut = $request->request->get('statut');
        $commande->setStatut($statut);
        $em->flush();
        return $this->redirectToRoute('admin_commande_detail', ['id' => $id]);
    }
    }
