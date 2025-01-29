<?php

namespace App\Entity;

use App\Enum\CurrencyValue;
use App\Repository\CoinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoinRepository::class)]
class Coin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: CurrencyValue::class)]
    private ?CurrencyValue $value = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?CurrencyValue
    {
        return $this->value;
    }

    public function setValue(CurrencyValue $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
