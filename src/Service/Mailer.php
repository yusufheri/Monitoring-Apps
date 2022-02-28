<?php

namespace App\Service;

use App\Entity\Status;
use App\Entity\Website;
use App\Repository\EmailReportRepository;
use Symfony\Component\Mailer\Mailer as MailerMailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    /**@var MailerInterface */
    private $mailerInterface;

    /**@var Environment */
    private $twig;

    private $emailReportRepository;

    public function __construct(MailerInterface $mailerInterface, Environment $twig, EmailReportRepository $emailReportRepository)
    {
        $this->emailReportRepository = $emailReportRepository;
        $this->mailerInterface = $mailerInterface;
        $this->twig = $twig;
    }

    public function send(array $websiteStatus)
    {
        $to = $this->emailReportRepository->createQueryBuilder('e')->select('e.email')
            ->getQuery()->getSingleColumnResult();

        $email = (new Email())
            ->from($_ENV['EMAIL_FROM'])
            ->to(...$to)
            ->subject('Monitoring Apps: Status websites')
            ->html($this->twig->render('admin/mail.html.twig', compact('websiteStatus')), 'text/html');

        $this->mailerInterface->send($email);
    }
}
