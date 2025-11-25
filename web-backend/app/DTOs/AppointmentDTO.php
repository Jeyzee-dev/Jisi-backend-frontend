<?php

namespace App\DTOs;

/**
 * AppointmentDTO: Data Transfer Object for Appointment data
 * Responsibilities: Structuring and validating appointment data transfer
 */
class AppointmentDTO
{
    public int $id;
    public int $userId;
    public ?int $serviceId;
    public string $appointmentDate;
    public string $appointmentTime;
    public string $status;
    public ?string $notes;
    public ?string $declineReason;
    public string $type;
    public string $createdAt;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->serviceId = $data['service_id'] ?? null;
        $this->appointmentDate = $data['appointment_date'] ?? '';
        $this->appointmentTime = $data['appointment_time'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->notes = $data['notes'] ?? null;
        $this->declineReason = $data['decline_reason'] ?? null;
        $this->type = $data['type'] ?? 'consultation';
        $this->createdAt = $data['created_at'] ?? now();
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'service_id' => $this->serviceId,
            'appointment_date' => $this->appointmentDate,
            'appointment_time' => $this->appointmentTime,
            'status' => $this->status,
            'notes' => $this->notes,
            'decline_reason' => $this->declineReason,
            'type' => $this->type,
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
