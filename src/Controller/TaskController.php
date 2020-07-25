<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Lapse;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index", methods={"GET"})
     */
    public function index(TaskRepository $taskRepository): Response
    {
        //get all tasks
        $tasks = $taskRepository->findAll();
        
        //for each task, find related lapses 
        foreach ($tasks as $task) {
            $seconds = 0;
            $lapses = $task->getLapses();

            foreach ($lapses as $lapse) {
                $seconds += $lapse->getComputedSeconds();
            }

            //get seconds to set the attribute
            $task->seconds = $seconds;
            //get formatted time to display in table
            $task->formattedTime = $this->secondsToTime($seconds);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request, TaskRepository $taskRepository) : JsonResponse
    {
        if($request->request->get('task')){

            $taskArray = $request->request->get('task');
            //Look for existing task
            $task = $taskRepository->findByName($taskArray['name']);
            $entityManager = $this->getDoctrine()->getManager();
            
            if(!$task) {
                //Create task if it doesn't exist
                $task = new Task($taskArray['name']);        
                $entityManager->persist($task);
            }          

            //Format DateTime
            $startDate = $this->milisecondsToDatetTime($taskArray['startTime']);        
            $endDate = $this->milisecondsToDatetTime($taskArray['endTime']);      

            //Create new Lapse and add it to the task
            $task->addLapse(new Lapse($startDate, $endDate));

            //Save task
            $entityManager->persist($task);
            $entityManager->flush();

            $status = ['output' => 'ok'];
            return new JsonResponse($status);
        }
    }


    //Format miliseconds comming from JS to PHP Datetime
    private function milisecondsToDatetTime($miliseconds) : \DateTime
    {
        $seconds = $miliseconds / 1000;
        $seconds += 60*60*2;
        $date = date("Y/m/d H:i:s", $seconds);
        return new \DateTime($date);
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
