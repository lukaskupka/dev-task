<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Infrastructure\Fixture\BarbershopFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'fixtures:load',
    description: 'Load database fixtures',
)]
final class LoadFixturesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('append', null, InputOption::VALUE_NONE, 'Append fixtures instead of purging the database first');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loader = new Loader();
        $loader->addFixture(new BarbershopFixtures());

        $purger   = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);

        $append = (bool) $input->getOption('append');

        if (!$append) {
            $io->warning('Purging database before loading fixtures...');
        }

        $executor->execute($loader->getFixtures(), $append);

        $io->success('Fixtures loaded successfully.');

        return Command::SUCCESS;
    }
}
