<?php

namespace App\Entity;

use App\Repository\LapseRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Task;

/**
 * @ORM\Entity(repositoryClass=LapseRepository::class)
 */
class Lapse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="lapses")
     * @ORM\JoinColumn(nullable=false, name="task_id", referencedColumnName="id")
    */
    private $task;



    public function __construct($startTime,  $endTime = null)
    {
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function getComputedSeconds(): ?Int
    {
        if ($this->getEndTime())
            return $this->getEndTime()->format('U') - $this->getStartTime()->format('U');
        else {
            $now = new \DateTime();
            return $now->format('U') - $this->getStartTime()->format('U');
        }
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

}
