<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppointmentRepository")
 */
class Appointment implements AppointmentInterface, CitizenInterface, LawyerInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="appointments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private $citizen;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="lawyerAppointments", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     * @Assert\Valid
     */
    private $lawyer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AppointmentDatetime", mappedBy="appointment", cascade={"persist", "remove"})
     */
    private $appointmentDatetimes;

    public function __construct()
    {
        $this->appointmentDatetimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCitizen(): ?UserInterface
    {
        return $this->citizen;
    }

    public function setCitizen(?UserInterface $citizen): self
    {
        $this->citizen = $citizen;

        return $this;
    }

    public function getLawyer(): ?UserInterface
    {
        return $this->lawyer;
    }

    public function setLawyer(?UserInterface $lawyer): self
    {
        $this->lawyer = $lawyer;

        return $this;
    }

    /**
     * @return Collection|AppointmentDatetime[]
     */
    public function getAppointmentDatetimes(): Collection
    {
        return $this->appointmentDatetimes;
    }

    public function addAppointmentDatetime(AppointmentDatetime $appointmentDatetime): self
    {
        if (!$this->appointmentDatetimes->contains($appointmentDatetime)) {
            $this->appointmentDatetimes[] = $appointmentDatetime;
            $appointmentDatetime->setAppointment($this);
        }

        return $this;
    }

    public function removeAppointmentDatetime(AppointmentDatetime $appointmentDatetime): self
    {
        if ($this->appointmentDatetimes->contains($appointmentDatetime)) {
            if ($appointmentDatetime->getAccepted() === null) {
                $this->appointmentDatetimes->removeElement($appointmentDatetime);
                // set the owning side to null (unless already changed)
                if ($appointmentDatetime->getAppointment() === $this) {
                    $appointmentDatetime->setAppointment(null);
                }
            }
        }

        return $this;
    }

    public function clearAppointmentDatetimes()
    {
        $this->appointmentDatetimes = new ArrayCollection();
    }
}
