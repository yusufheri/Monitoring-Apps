<?php

namespace App\Entity;

use App\Repository\EmailReportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailReportRepository::class)
 */
class EmailReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $lastReportAt;

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

    public function getLastReportAt(): ?\DateTimeImmutable
    {
        return $this->lastReportAt;
    }

    public function setLastReportAt(\DateTimeImmutable $lastReportAt): self
    {
        $this->lastReportAt = $lastReportAt;

        return $this;
    }
}
