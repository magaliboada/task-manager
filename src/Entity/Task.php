<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $Name;

    /**
     * @ORM\OneToMany(targetEntity=Lapse::class, mappedBy="task", orphanRemoval=true, cascade={"persist"})
     */
    private $lapses;

    public function __construct(String $name)
    {
        $this->Name = $name;
        $this->lapses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection|Lapse[]
     */
    public function getLapses(): Collection
    {
        return $this->lapses;
    }

    public function addLapse(Lapse $lapse): self
    {
        if (!$this->lapses->contains($lapse)) {
            $this->lapses[] = $lapse;
            $lapse->setTask($this);
        }

        return $this;
    }

    public function removeLapse(Lapse $lapse): self
    {
        if ($this->lapses->contains($lapse)) {
            $this->lapses->removeElement($lapse);
            // set the owning side to null (unless already changed)
            if ($lapse->getTask() === $this) {
                $lapse->setTask(null);
            }
        }

        return $this;
    }
}
