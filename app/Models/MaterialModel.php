<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['course_id', 'title', 'file_name', 'file_path', 'created_at'];

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
     * Get the count of materials for a specific course.
     *
     * @param int $courseId
     * @return int
     */
    public function getMaterialsCountByCourse($courseId)
    {
        return $this->where('course_id', $courseId)->countAllResults();
    }

    /**
     * Get all materials for a specific course.
     *
     * @param int $courseId
     * @return array
     */
    public function getMaterialsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all materials for courses the user is enrolled in.
     *
     * @param int $userId
     * @return array
     */
    public function getMaterialsByUserId($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('materials.*, courses.title as course_name');
        $builder->join('courses', 'courses.id = materials.course_id');
        $builder->join('enrollments', 'enrollments.course_id = materials.course_id');
        $builder->where('enrollments.user_id', $userId);
        $builder->orderBy('materials.created_at', 'DESC');

        return $builder->get()->getResultArray();
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
