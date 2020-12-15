<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Framework\ORM\Model\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="request")
 */
class Request extends AbstractModel
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int|null
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", name="message")
     *
     * @var string
     */
    private string $message;

    /**
     * @ORM\Column(type="string", name="token")
     *
     * @var string|null
     */
    private ?string $token = null;

    /**
     * @ORM\Column(type="string", name="status")
     * @var string
     */
    private string $status;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @inheritDoc
     */
    public function toResponse(): array
    {
        return [
            "id" => $this->getId(),
            "message" => $this->getMessage(),
            "token" => $this->getToken(),
            "status" => $this->getStatus()
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "message" => $this->getMessage(),
            "token" => $this->getToken(),
            "status" => $this->getStatus()
        ];
    }
}