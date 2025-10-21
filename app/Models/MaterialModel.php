<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];

    /**
     * Insert a new material record.
     *
     * @param array $data Array with 'course_id', 'file_name', 'file_path' keys.
     * @return int|bool Insert ID on success, false on failure.
     */
    public function insertMaterial($data)
    {
        // Set created_at to now if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course.
     *
     * @param int $course_id The course's ID.
     * @return array Array of material records.
     */
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }

    /**
     * Get a material by ID.
     *
     * @param int $material_id The material's ID.
     * @return array|null Material record or null if not found.
     */
    public function getMaterialById($material_id)
    {
        return $this->find($material_id);
    }

    /**
     * Delete a material by ID.
     *
     * @param int $material_id The material's ID.
     * @return bool True on success, false on failure.
     */
    public function deleteMaterial($material_id)
    {
        return $this->delete($material_id);
    }
}
