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

class TaskListCommand extends Command
{
    // To execute on root project: php bin/console task:list
    protected static $defaultName = 'task:list';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $tasks = $this->entityManager->getRepository("App:Task")->findAll();
        
        foreach ($tasks as $task) {
            $output->writeln([                
                'Task Name: ' . $task->getName(),
            ]);  

            //check if there's a lapse with endTime on null and output it's running
            $lapse = $this->entityManager->getRepository("App:Lapse")->findByTaskAndNullEnd($task);
            $seconds = 0;
            
            if($lapse) {
                $output->writeln([
                    'Task currently running.'
                ]);
            } else {
                $output->writeln([
                    'Task ended.'
                ]);
            }
              
            foreach ($task->getLapses() as $lapse) {
                $output->write([
                    'Started at: ' . $lapse->getStartTime()->format('H:i:s') . '    |   ',
                ]);
                if ( $lapse->getEndTime() )
                    $output->write([
                        'Ended at: ' . $lapse->getEndTime()->format('H:i:s') . "\n",
                    ]);
                else {
                    $output->write([
                        "Running\n",
                    ]);
                }

                $seconds += $lapse->getComputedSeconds();

            }

            $output->writeln([
                'Total: ' . $this->secondsToTime($seconds),
            ]);
            
            $output->writeln([
                '________________________________________________',
            ]);
        }                            
        
        $this->entityManager->flush();
        return Command::SUCCESS;
    }

    //Format seconds to Time for template displaying
    private function secondsToTime($seconds) : String
    {
        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);

        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);;
    }
}