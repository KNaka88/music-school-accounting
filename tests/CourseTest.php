<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Course.php";
    // require_once "src/Student.php";
    // require_once "src/Teacher.php";

    $server = 'mysql:host=localhost:8889;dbname=crm_music_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class CourseTest extends PHPUnit_Framework_TestCase{

        function test_construct()
        {
            // Arrange
            $input_title = "Basket weaving";
            $input_id = 1;
            $test_course = new Course($input_title, $input_id);
            // Act
            $result1 = $test_course->getTitle();
            $result2 = $test_course->getId();

            // Assert
            $this->assertEquals($input_title, $result1);
            $this->assertEquals($input_id, $result2);

        }
    }




 ?>