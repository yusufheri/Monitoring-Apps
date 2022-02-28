<?php

namespace App\Controller;

use App\Entity\Website;
use App\Form\WebsiteType;
use App\Repository\WebsiteRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/dashboard", name="app_admin")
     */
    public function index(WebsiteRepository $websiteRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'websites' => $websiteRepository->findAll(),
        ]);
    }

    /**
     * Enregistre un nouveau website
     * @Route("/dashboard/website/new", name="app_admin_website_new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $website = new Website();
        $form = $this->createForm(WebsiteType::class, $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $website->setCreatedAt(new DateTimeImmutable());

            $this->manager->persist($website);
            $this->manager->flush();

            $this->addFlash('success', 'Nouveau site enregistré avec succès !');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/website/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * dEditer un website
     * @Route("/dashboard/website/{id}/edit", name="app_admin_website_edit")
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request, Website $website): Response
    {
        $form = $this->createForm(WebsiteType::class, $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'Website modifié avec succès !');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/website/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete un website
     * @Route("/dashboard/website/{id}/delete", name="app_admin_website_delete")
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, Website $website): Response
    {
        $statues = $website->getStatuses();
        foreach ($statues as $status) {
            $this->manager->remove($status);
        }
        $this->manager->remove($website);
        $this->manager->flush();

        $this->addFlash('success', 'Website supprimé avec succès !');
        return $this->redirectToRoute('app_admin');
    }
}
