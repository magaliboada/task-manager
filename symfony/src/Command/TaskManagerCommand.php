<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Entity\Lapse;
use Doctrine\ORM\EntityManagerInterface;

class TaskManagerCommand extends Command
{
    // To execute on root project: php bin/console task:manager task_name [start or stop]
    protected static $defaultName = 'task:manager';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->addArgument('task', InputArgument::REQUIRED, 'Name of the task')
        ->addArgument('status', InputArgument::REQUIRED, 'Starting or ending task')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $task = $this->entityManager->getRepository("App:Task")->findByName($input->getArgument('task'));

        if(!$task) {
            //Create task if it doesn't exist
            $task = new Task($input->getArgument('task'));        
            $this->entityManager->persist($task);

            $output->writeln([
                'Task Created:',
                $task->getName(),
            ]);
        }          

        //Create new Lapse and add it to the task
        if($input->getArgument('status') == 'start') {

            //check if there's a lapse running for that task that hasn't ended and ask to stop it in case it exists
            $lapse = $this->entityManager->getRepository("App:Lapse")->findByTaskAndNullEnd($task);
            if($lapse) {
                $output->writeln([
                    'Task running, please end it first.'
                ]);
            }
            else {
                $output->writeln([
                    'Task starting:',
                    $task->getName(),
                ]);
                $task->addLapse(new Lapse(new \DateTime()));
                $this->entityManager->persist($task);
            }
        } elseif ($input->getArgument('status') == 'end') {

            //check if there's a lapse running for that task that hasn't ended and end it
            $lapse = $this->entityManager->getRepository("App:Lapse")->findByTaskAndNullEnd($task);

            if($lapse) {
                $lapse->setEndTime(new \DateTime());
                $this->entityManager->persist($lapse);

                $output->writeln([
                    'Task Ended:',
                    $task->getName(),
                ]);                
            } else {
                $output->writeln([
                    'No task started with that name.',
                ]);
            }            
        }
        
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}