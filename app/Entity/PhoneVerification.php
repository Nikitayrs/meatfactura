<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'phoneVerifications')]
#[UniqueEntity('phone', message: "Этот телефон уже используется для верификации")]
class PhoneVerification
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: "Телефон обязателен для верификации")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Номер телефона не может превышать {{ limit }} символов"
    )]
    private string $phone;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank(message: "Код верификации обязателен")]
    #[Assert\Length(
        min: 4,
        max: 10,
        minMessage: "Код должен содержать минимум {{ limit }} символа",
        maxMessage: "Код не может превышать {{ limit }} символов"
    )]
    private string $code;

    #[ORM\Column(name: 'expires_at', type: 'datetimetz')]
    #[Assert\NotBlank(message: "Время истечения обязательно")]
    #[Assert\Type("\DateTimeInterface", message: "Значение должно быть датой/временем")]
    #[Assert\GreaterThan(
        "now",
        message: "Время истечения должно быть в будущем"
    )]
    private DateTime $expiresAt;

    #[ORM\Column(name: 'created_at')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at')]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTime();
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTime $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}