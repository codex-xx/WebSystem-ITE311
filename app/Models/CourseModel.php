<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['course_code', 'title', 'description', 'teacher_id', 'school_year', 'semester', 'schedule_days', 'schedule_time_start', 'schedule_time_end', 'status', 'start_date', 'end_date'];

    protected $validationRules = [
        'course_code' => 'required|min_length[1]|max_length[50]',
        'title' => 'required|min_length[1]|max_length[255]',
        'description' => 'permit_empty',
        'teacher_id' => 'permit_empty|integer',
        'school_year' => 'required|max_length[20]',
        'semester' => 'required|in_list[1st,2nd,Summer]',
        'schedule_days' => 'permit_empty|max_length[100]',
        'schedule_time_start' => 'permit_empty|valid_time',
        'schedule_time_end' => 'permit_empty|valid_time',
        'status' => 'required|in_list[Active,Inactive]',
        'start_date' => 'permit_empty|valid_date',
        'end_date' => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'course_code' => [
            'required' => 'Course code is required.',
        ],
        'title' => [
            'required' => 'Course title is required.',
        ],
        'school_year' => [
            'required' => 'School year is required.',
        ],
        'semester' => [
            'required' => 'Semester is required.',
            'in_list' => 'Semester must be 1st, 2nd, or Summer.',
        ],
    ];

    /**
     * Override validate to add custom date validation.
     */
    public function validate($row = null): bool
    {
        $result = parent::validate($row);

        if ($result && isset($row['start_date']) && isset($row['end_date']) && !empty($row['start_date']) && !empty($row['end_date'])) {
            $startDate = strtotime($row['start_date']);
            $endDate = strtotime($row['end_date']);

            if ($startDate >= $endDate) {
                $this->errors['end_date'] = 'End date must be after start date.';
                return false;
            }
        }

        return $result;
    }

    /**
     * Override insert to add validation for uniqueness and teacher conflicts.
     */
    public function insert($row = null, bool $returnID = true)
    {
        // Run standard validation
        if (!$this->validate($row)) {
            return false;
        }

        // Check for duplicate course_code in same school_year and semester
        if (isset($row['course_code']) && isset($row['school_year']) && isset($row['semester'])) {
            $existing = $this->where('course_code', $row['course_code'])
                            ->where('school_year', $row['school_year'])
                            ->where('semester', $row['semester'])
                            ->first();
            if ($existing) {
                $this->errors['course_code'] = 'Course code already exists for this school year and semester.';
                return false;
            }
        }

        // Check teacher schedule conflict if teacher_id provided
        if (isset($row['teacher_id']) && $row['teacher_id'] && !$this->isTeacherScheduleValid($row)) {
            $this->errors['schedule_days'] = 'Teacher has a schedule conflict with this course.';
            return false;
        }

        return parent::insert($row, $returnID);
    }

    /**
     * Override update to add validation for teacher conflicts.
     */
    public function update($id = null, $row = null): bool
    {
        // Run standard validation
        if (!$this->validate($row)) {
            return false;
        }

        // Check teacher schedule conflict if teacher_id provided
        if (isset($row['teacher_id']) && $row['teacher_id'] && !$this->isTeacherScheduleValid($row, $id)) {
            $this->errors['schedule_days'] = 'Teacher has a schedule conflict with this course.';
            return false;
        }

        return parent::update($id, $row);
    }

    /**
     * Custom method for updating schedule with separate validation data.
     * This allows passing teacher_id for validation without updating it.
     */
    public function validateAndUpdate($id = null, $updateData = null, $validationData = null)
    {
        // Run standard validation on update data
        if (!$this->validate($updateData)) {
            return false;
        }

        // Check teacher schedule conflict using validation data
        if (isset($validationData['teacher_id']) && $validationData['teacher_id'] && !$this->isTeacherScheduleValid($validationData, $id)) {
            $this->errors['schedule_days'] = 'Teacher has a schedule conflict with this course.';
            return false;
        }

        return parent::update($id, $updateData);
    }

    /**
     * Check if the teacher's schedule has conflicts with the proposed course.
     *
     * @param array $courseData Proposed course data
     * @param int|null $excludeCourseId ID to exclude from check (for updates)
     * @return bool True if valid, false if conflict
     */
    public function isTeacherScheduleValid($courseData, $excludeCourseId = null)
    {
        if (!isset($courseData['teacher_id']) || !$courseData['teacher_id']) {
            return true; // No teacher, no conflict
        }

        // Get courses for this teacher in same school year and semester
        $query = $this->where('teacher_id', $courseData['teacher_id'])
                      ->where('school_year', $courseData['school_year'])
                      ->where('semester', $courseData['semester']);

        if ($excludeCourseId) {
            $query = $query->where('id !=', $excludeCourseId);
        }

        $teacherCourses = $query->findAll();

        foreach ($teacherCourses as $existing) {
            if ($this->timesOverlap($courseData, $existing)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if two courses have overlapping times on the same days.
     *
     * @param array $newCourse New course data
     * @param array $existing Existing course
     * @return bool True if overlap
     */
    private function timesOverlap($new, $existing)
    {
        // Check if they share any days
        $newDays = array_map('trim', explode(',', strtolower($new['schedule_days'] ?? '')));
        $existingDays = array_map('trim', explode(',', strtolower($existing['schedule_days'] ?? '')));

        $commonDays = array_intersect($newDays, $existingDays);
        if (empty($commonDays)) {
            return false;
        }

        // Check if times overlap
        $newStart = $new['schedule_time_start'] ? strtotime($new['schedule_time_start']) : false;
        $newEnd = $new['schedule_time_end'] ? strtotime($new['schedule_time_end']) : false;
        $existingStart = $existing['schedule_time_start'] ? strtotime($existing['schedule_time_start']) : false;
        $existingEnd = $existing['schedule_time_end'] ? strtotime($existing['schedule_time_end']) : false;

        // If either course doesn't have valid times, no overlap
        if ($newStart === false || $newEnd === false || $existingStart === false || $existingEnd === false) {
            return false;
        }

        return ($newStart < $existingEnd && $newEnd > $existingStart);
    }
}
