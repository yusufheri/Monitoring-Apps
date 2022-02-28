<?php

namespace App\Command;

use App\Entity\Status;
use App\Repository\WebsiteRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsitesCheckStatusCommand extends Command
{
    protected static $defaultName = 'app:websites:check-status';
    protected static $defaultDescription = 'Diagnostiquer le status de websites';

    private $mailer;
    private $manager;
    private $webSiteRepository;

    public function __construct(EntityManagerInterface $manager, WebsiteRepository $webSiteRepository, Mailer $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->webSiteRepository = $webSiteRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, 'Preciser l\'url à diagnostiquer')
            ->setDescription(self::$defaultDescription)
            ->setHelp('Cette commande permet de diagnostiquer les statuts de websites enregistrés dans la base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getOption('url');

        $io->writeln([
            "Diagnostic des statuts de websites ",
            "------------------------------------"
        ]);

        if ($url) $websites = $this->webSiteRepository->findBy(["url" => $url]);
        else $websites = $this->webSiteRepository->findAll();

        // creates a new progress bar
        $progressBar = new ProgressBar($output, count($websites));
        $progressBar->setFormat('verbose');

        // Creates a new table 
        $table = new Table($output);
        $table->setHeaders(["#", "Url", "Status"]);

        $websitesStatusFailure = [];

        // starts and displays the progress bar
        $progressBar->start();

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

            $this->manager->persist($status);

            if ($code == 500 || $code == 0)  $websitesStatusFailure[$website->getUrl()] = $status;

            // advances the progress bar 1 unit
            $progressBar->advance();
            //save info in table
            $table->addRow([$key + 1, $url, $code]);
        }

        $table->addRows([
            new TableSeparator(),
            [new TableCell('Merci  beaucoup pour la patience.', ['colspan' => 3])]
        ]);

        $this->manager->flush();
        $progressBar->finish();
        $io->writeln("");
        $table->render();
        $io->success('Le diagnostic a été effectué avec succès.');

        if (count($websitesStatusFailure) > 0) $this->mailer->send($websitesStatusFailure);
        return Command::SUCCESS;
    }
}
