<?php

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $usd = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $eur = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $rub = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getUsd(): ?string
    {
        return $this->usd;
    }

    public function setUsd(string $usd): self
    {
        $this->usd = $usd;

        return $this;
    }

    public function getEur(): ?string
    {
        return $this->eur;
    }

    public function setEur(string $eur): self
    {
        $this->eur = $eur;

        return $this;
    }

    public function getRub(): ?string
    {
        return $this->rub;
    }

    public function setRub(string $rub): self
    {
        $this->rub = $rub;

        return $this;
    }
}
