<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Support\Collection;

/**
 * AppointmentRepository: Handles data access for appointments
 * Responsibilities: Database queries for appointment operations
 */
class AppointmentRepository
{
    protected Appointment $model;

    public function __construct(Appointment $model = null)
    {
        $this->model = $model ?? new Appointment();
    }

    /**
     * Get all appointments
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get appointment by ID
     */
    public function getById(int $id): ?Appointment
    {
        return $this->model->find($id);
    }

    /**
     * Get appointments with pagination
     */
    public function paginate(int $perPage = 15)
    {
        return $this->model->with('user')->paginate($perPage);
    }

    /**
     * Create appointment
     */
    public function create(array $data): Appointment
    {
        return $this->model->create($data);
    }

    /**
     * Update appointment
     */
    public function update(int $id, array $data): bool
    {
        $appointment = $this->getById($id);
        if (!$appointment) return false;
        return $appointment->update($data);
    }

    /**
     * Delete appointment
     */
    public function delete(int $id): bool
    {
        $appointment = $this->getById($id);
        if (!$appointment) return false;
        return $appointment->delete();
    }

    /**
     * Get appointments by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get appointments by user ID
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Get appointments by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('appointment_date', [$startDate, $endDate])->get();
    }

    /**
     * Get appointments by date
     */
    public function getByDate(string $date): Collection
    {
        return $this->model->where('appointment_date', $date)->get();
    }

    /**
     * Get pending appointments
     */
    public function getPending(): Collection
    {
        return $this->model->where('status', 'pending')->get();
    }

    /**
     * Get approved appointments
     */
    public function getApproved(): Collection
    {
        return $this->model->where('status', 'approved')->get();
    }
}
