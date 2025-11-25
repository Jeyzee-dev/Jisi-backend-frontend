<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;
use Exception;

/**
 * ServiceService: Handles all service-related business logic
 * Responsibilities: Service management, CRUD operations
 */
class ServiceService
{
    /**
     * Create a new service
     */
    public function createService(array $data): Service
    {
        try {
            $service = Service::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'duration' => $data['duration'] ?? 60,
                'price' => $data['price'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return $service;
        } catch (Exception $e) {
            throw new Exception('Failed to create service: ' . $e->getMessage());
        }
    }

    /**
     * Update service
     */
    public function updateService(Service $service, array $data): Service
    {
        try {
            $service->update([
                'name' => $data['name'] ?? $service->name,
                'description' => $data['description'] ?? $service->description,
                'duration' => $data['duration'] ?? $service->duration,
                'price' => $data['price'] ?? $service->price,
                'is_active' => $data['is_active'] ?? $service->is_active,
            ]);

            return $service;
        } catch (Exception $e) {
            throw new Exception('Failed to update service: ' . $e->getMessage());
        }
    }

    /**
     * Delete service
     */
    public function deleteService(Service $service): bool
    {
        try {
            return $service->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete service: ' . $e->getMessage());
        }
    }

    /**
     * Get all active services
     */
    public function getActiveServices(): Collection
    {
        try {
            return Service::where('is_active', true)->where('deleted_at', null)->get();
        } catch (Exception $e) {
            throw new Exception('Failed to fetch services: ' . $e->getMessage());
        }
    }

    /**
     * Get all services
     */
    public function getAllServices(): Collection
    {
        try {
            return Service::all();
        } catch (Exception $e) {
            throw new Exception('Failed to fetch services: ' . $e->getMessage());
        }
    }
}
