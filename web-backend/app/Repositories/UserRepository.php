<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * UserRepository: Handles data access for users
 * Responsibilities: Database queries for user operations
 */
class UserRepository
{
    protected User $model;

    public function __construct(User $model = null)
    {
        $this->model = $model ?? new User();
    }

    /**
     * Get all users
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get user by ID
     */
    public function getById(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get users with pagination
     */
    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Create user
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->getById($id);
        if (!$user) return false;
        return $user->update($data);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $user = $this->getById($id);
        if (!$user) return false;
        return $user->delete();
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): Collection
    {
        return $this->model->where('role', $role)->get();
    }

    /**
     * Get active users
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * Get inactive users
     */
    public function getInactive(): Collection
    {
        return $this->model->where('is_active', false)->get();
    }

    /**
     * Search users
     */
    public function search(string $term): Collection
    {
        return $this->model->where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->get();
    }
}
