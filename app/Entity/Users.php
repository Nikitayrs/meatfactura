<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[UniqueEntity('phone', message: "Этот телефонный номер уже зарегистрирован")]
class Users
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(name: 'phone', type: 'string', length: 255, unique: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Номер телефона не может превышать {{ limit }} символов"
    )]
    private string $phone;

    #[ORM\Column(name: 'name', length: 30)]
    #[Assert\NotBlank(message: "Имя обязательно для заполнения")]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: "Имя должно содержать минимум {{ limit }} символа",
        maxMessage: "Имя не может превышать {{ limit }} символов"
    )]
    private string $name;

    #[ORM\Column(name: 'address', nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Адрес не может превышать {{ limit }} символов"
    )]
    private ?string $address = null;

    #[ORM\Column(name: 'password')]
    #[Assert\NotBlank(message: "Пароль обязателен")]
    #[Assert\Length(
        min: 6,
        max: 255,
        minMessage: "Пароль должен содержать минимум {{ limit }} символов",
        maxMessage: "Пароль не может превышать {{ limit }} символов"
    )]
    private string $password;

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
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    // Геттеры и сеттеры
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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