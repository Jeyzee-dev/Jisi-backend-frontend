<?php

namespace App\DTOs;

/**
 * UserDTO: Data Transfer Object for User data
 * Responsibilities: Structuring and validating user data transfer
 */
class UserDTO
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public string $role;
    public bool $isActive;
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->firstName = $data['first_name'] ?? '';
        $this->lastName = $data['last_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->role = $data['role'] ?? 'client';
        $this->isActive = $data['is_active'] ?? true;
        $this->createdAt = $data['created_at'] ?? now();
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * Convert DTO to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
