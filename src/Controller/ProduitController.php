<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProduitType;
use App\Entity\Produit;
use App\Service\PanierService;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produits')]
    public function index(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        Request $request,
    ): Response {
        $categorieId = $request->query->get('categorie');

        if ($categorieId) {
            $produits = $produitRepository->findBy(['categorie' => $categorieId]);
        } else {
            $produits = $produitRepository->findAll();
        }

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/produit/new', name: 'produit_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $produit->setImage($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('app_produits');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}/edit', name: 'produit_edit')]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($produit->getStock() < 0) {
                $this->addFlash('error', 'Stock invalide');
                return $this->redirectToRoute('app_produits');
            }

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {}

                $produit->setImage($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('app_produits');
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/{id}', name: 'produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {

            if (count($produit->getLigneCommandes()) > 0) {
                $this->addFlash('error', 'Produit utilisé dans une commande');
                return $this->redirectToRoute('app_produits');
            }

            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/produit/{id}', name: 'produit_show', methods: ['GET'])]
    public function show(Produit $produit, Request $request): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'categorie' => $request->query->get('categorie'),
        ]);
    }

    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function addToCart(int $id, PanierService $panierService): Response
    {
        $panierService->add($id);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/panier/update/{id}/{quantite}', name: 'panier_update')]
    public function updateCart(int $id, int $quantite, PanierService $panierService): Response
    {
        $panierService->update($id, $quantite);

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/panier/remove/{id}', name: 'panier_remove')]
    public function removeFromCart(int $id, PanierService $panierService): Response
    {
        $panierService->remove($id);

        return $this->redirectToRoute('app_produits');
    }
}
