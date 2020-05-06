<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    const CITIZEN         = 'citizen';
    const LAWYER          = 'lawyer';
    const AVAILABLE_TYPES = [self::CITIZEN => self::CITIZEN, self::LAWYER => self::LAWYER];

    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appointment", mappedBy="citizen")
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appointment", mappedBy="lawyer")
     */
    private $lawyerAppointments;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->lawyerAppointments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setCitizen($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->contains($appointment)) {
            $this->appointments->removeElement($appointment);
            // set the owning side to null (unless already changed)
            if ($appointment->getCitizen() === $this) {
                $appointment->setCitizen(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Appointment[]
     */
    public function getLawyerAppointments(): Collection
    {
        return $this->lawyerAppointments;
    }

    public function addLawyerAppointment(Appointment $appointment): self
    {
        if (!$this->lawyerAppointments->contains($appointment)) {
            $this->lawyerAppointments[] = $appointment;
            $appointment->setLawyer($this);
        }

        return $this;
    }

    public function removeLawyerAppointment(Appointment $appointment): self
    {
        if ($this->lawyerAppointments->contains($appointment)) {
            $this->lawyerAppointments->removeElement($appointment);
            // set the owning side to null (unless already changed)
            if ($appointment->getLawyer() === $this) {
                $appointment->setLawyer(null);
            }
        }

        return $this;
    }

}
