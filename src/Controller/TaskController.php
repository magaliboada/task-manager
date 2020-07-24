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
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request, TaskRepository $taskRepository)
    {

        if($request->request->get('task')){
            //make something curious, get some unbelieveable data
            $status = ['output' => true];


            $taskArray = $request->request->get('task');

            $task = $taskRepository->findByName($taskArray['name']);
            $entityManager = $this->getDoctrine()->getManager();
            
            if(!$task) {

                $task = new Task($taskArray['name']);        
                $entityManager->persist($task);
            }

            
            

            // $task = new Task();
            $seconds = $taskArray['startTime'] / 1000;
            $startDate = date("Y/m/d H:i:s", $seconds);

            $seconds = $taskArray['endTime'] / 1000;
            $endDate = date("Y/m/d H:i:s", $seconds);
            $lapse  = new Lapse($task, $startDate, $endDate);

            $entityManager->persist($lapse);
            $entityManager->flush();

            // $startDate = date("Y-m-d h:i:s",$taskArray['startTime']);
            // $endDate = date("Y-m-d h:i:s",$taskArray['endTime']);

            
            $task->addLapse($lapse);
            




            return new JsonResponse($endDate);
        }
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="task_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index');
    }
}
