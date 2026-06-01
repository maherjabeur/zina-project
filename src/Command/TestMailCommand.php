<?php

namespace App\Command;

use App\Service\PhpMailerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-mail',
    description: 'Envoie un email de test avec PHPMailer ou genere un dry-run local.',
)]
class TestMailCommand extends Command
{
    public function __construct(private readonly PhpMailerService $mailer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('recipient', InputArgument::OPTIONAL, 'Adresse email destinataire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipient = $input->getArgument('recipient') ?: $this->mailer->getAdminEmail();

        $result = $this->mailer->sendHtml(
            $recipient,
            'Test email Bella Couture',
            '<h1>Test email Bella Couture</h1><p>PHPMailer est bien branche sur le projet.</p>'
        );

        if ($result) {
            $io->success('Email de test genere en dry-run : ' . $result);
            $io->note('Pour envoyer reellement, configurez PHPMAILER_HOST et mettez PHPMAILER_DRY_RUN=false.');
        } else {
            $io->success('Email de test envoye a ' . $recipient . ' avec PHPMailer.');
        }

        return Command::SUCCESS;
    }
}
