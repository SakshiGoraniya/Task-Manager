<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tasks:report',
    description: 'Display task count per status for each user'
)]
class TasksReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $output->writeln("User: {$user->getName()}");
            
            $statusCounts = $this->entityManager->createQuery(
                'SELECT t.status, COUNT(t.id) as count 
                 FROM App\Entity\Task t 
                 WHERE t.user = :user 
                 GROUP BY t.status'
            )
            ->setParameter('user', $user)
            ->getResult();

            foreach ($statusCounts as $statusCount) {
                $output->writeln("  - {$statusCount['status']}: {$statusCount['count']}");
            }
            
            $output->writeln('');
        }

        return Command::SUCCESS;
    }
}