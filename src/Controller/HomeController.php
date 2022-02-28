<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Website;
use App\Repository\StatusRepository;
use App\Repository\WebsiteRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="app_home")
     */
    public function index(WebsiteRepository $websiteRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'websites' => $websiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/websites/analyze", name="app_analyze")
     */
    public function analyze(WebsiteRepository $websiteRepository, EntityManagerInterface $entityManagerInterface, Mailer $mailer)
    {
        $websites = $websiteRepository->findAll();
        $websitesStatusFailure = [];

        foreach ($websites as $key => $website) {

            $url = $website->getUrl();
            $url = str_contains($url, "http") ? $url : "http://" . $url;

            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($handle);
            $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            $status = new Status();
            $status
                ->setReportedAt(new \DateTimeImmutable())
                ->setCode($code)
                ->setWebsite($website);

            if ($code == 500 || $code == 0)  $websitesStatusFailure[$website->getUrl()] = $status;

            $entityManagerInterface->persist($status);
        }

        $entityManagerInterface->flush();

        if (count($websitesStatusFailure) > 0) $mailer->send($websitesStatusFailure);

        $this->addFlash('success', 'Le diagnostic a bien été effectué avce succès.');
        return $this->redirectToRoute("app_home");
    }

    /**
     * @Route("/websites/clean", name="app_clean_history_websites")
     */
    public function cleanStatusHistory(StatusRepository $statusRepository)
    {
        $statusRepository->cleanStatusHistory();
        $this->addFlash('warning', 'L\'historique des status a bien été effacé avec succès.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/{id}/websites", name="app_show_website")
     */
    public function show(Website $website)
    {
        return $this->render('home/show.html.twig', [
            'website' => $website
        ]);
    }
}
