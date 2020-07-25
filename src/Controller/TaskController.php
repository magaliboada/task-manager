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
        $tasks = $taskRepository->findAll();
        
        foreach ($tasks as $key => $task) {
            $lapses = $task->getLapses();

            // echo var_export($lapses, true);
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
            $task = $taskRepository->findByName($taskArray['name']);
            $entityManager = $this->getDoctrine()->getManager();
            
            if(!$task) {

                $task = new Task($taskArray['name']);        
                $entityManager->persist($task);
            }          
            
            $seconds = $taskArray['startTime'] / 1000;
            $startDate = date("Y/m/d H:i:s", $seconds);

            $seconds = $taskArray['endTime'] / 1000;
            $endDate = date("Y/m/d H:i:s", $seconds);
            $lapse  = new Lapse($task, $startDate, $endDate);

            $entityManager->persist($lapse);
            $entityManager->flush();

            $status = ['output' => true];
            return new JsonResponse($status);
        }
    }

}
