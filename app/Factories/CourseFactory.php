<?php
namespace App\Factories;

use App\Models\BaseCourse;

class CourseFactory
{
    /**
     *   @method build
     *   @param  string $courseName
     *   @return BaseCourse
     */
    public static function build(string $courseName)
    {
        $fullCourseClassName = "\\App\\Models\\Courses\\$courseName";
        if (!class_exists($fullCourseClassName)) {
            throw new \Exception("Cant find $courseName class in \\App\\Models\\Courses namespace");
        }

        $course = new $fullCourseClassName();
        return $course;
    }
}
