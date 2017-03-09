<?php
    class Student
    {
        private $student_name;
        private $notes;
        private $id;

        function __construct($student_name, $id = null)
        {
            $this->student_name = $student_name;
            $this->id = (Int)$id;
        }

        function setName($new_student_name)
        {
            $this->name = (string) $new_student_name;
        }

        function getName()
        {
            return $this->student_name;
        }

        function setNotes($new_note)
        {
            $this->notes = $new_note . $this->notes;

        }

        function getNotes()
        {
            return $this->notes;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {

          $GLOBALS['DB']->exec("INSERT INTO students (student_name, notes) VALUES ('{$this->getName()}', '{$this->getNotes()}');");
          $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM students;");

        }

        static function deleteJoin()
        {
            $GLOBALS['DB']->exec("DELETE FROM students_courses;");
        }

        static function getAll()
        {
            $returned_students = $GLOBALS['DB']->query("SELECT * FROM students;");
            $students = array();
            foreach($returned_students as $student){
                $name = $student['student_name'];
                $notes = $student['notes'];
                $id = $student['id'];
                $new_student = new Student($name, $id);
                $new_student->setNotes($notes);
                array_push($students, $new_student);

          }
          return $students;
        }

        function updateNotes($new_note)
        {
            $GLOBALS['DB']->exec("UPDATE students SET notes = '{$new_note}' WHERE id = {$this->getId()};");
            $this->setNotes($new_note);
        }

        function updateName($update)
        {
            $GLOBALS['DB']->exec("UPDATE students SET student_name = '{$update}' WHERE id = {$this->getId()};");
            $this->setName($update);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM students WHERE id = {$this->getId()};");
        }

        static function find($search_id)
        {
           $found_student = null;
           $students = Student::getAll();
           foreach($students as $student){
               $student_id = $student->getId();
               if ( $student_id == $search_id){
                   $found_student = $student;
               }
           }
           return $found_student;
        }

        function getTeachers()
        {
            $query = $GLOBALS['DB']->query("SELECT teachers.* FROM
            students JOIN students_teachers ON students.id = students_teachers.student_id
                     JOIN teachers ON students_teachers.teacher_id = teachers.id
                     WHERE students.id = {$this->getId()};");
            $teachers = array();
            foreach ($query as $teacher) {
                $teacher_name = $teacher['teacher_name'];
                $instrument = $teacher['instrument'];
                $notes= $teacher['notes'];
                $id = $teacher['id'];
                $found_teacher = new Teacher($teacher_name, $instrument, $id);
                $found_teacher->setNotes($notes);
                array_push($teachers, $found_teacher);
            }
            return $teachers;
        }

        function addTeacher($teacher_id)
        {

            $GLOBALS['DB']->exec("INSERT INTO students_teachers (student_id, teacher_id) VALUES ({$this->getId()}, {$teacher_id});");
        }

        function addCourse($course_id)
        {
            $today = date('Y-m-d h:i:s');
            // $today = '2017-3-6 10:10:10';

            $GLOBALS['DB']->exec("INSERT INTO courses_students (course_id, student_id, date_of_join) VALUES ({$course_id}, {$this->getId()}, '{$today}');");

            //
            // $check_duplication = false;
            // $query = $GLOBALS['DB']->query("SELECT * FROM courses_students WHERE course_id = {$course_id} AND student_id = {$this->id};");
            // var_dump($query);
            // $retrieved = $query->fetchAll(PDO::FETCH_ASSOC);
            //
            //
            // foreach($retrieved as $registration){
            //     $student_id = $registration['student_id'];
            //     $courseid = $registration['course_id'];
            //
            //     if($student_id == $this->id && $courseid  == $course_id){
            //         $check_duplication = true;
            //     }
            // }
            //
            // if($check_duplication == false ){
            //     $GLOBALS['DB']->exec("INSERT INTO courses_students (course_id, student_id, date_of_enrollment) VALUES ({$course_id}, {$this->id}, '{$today}');");
            // };
        }

        function getCourses()
        {
            $returned_courses = $GLOBALS['DB']->query("SELECT courses.* FROM
            students JOIN courses_students ON (students.id = courses_students.student_id)
                    JOIN courses ON (courses_students.course_id = courses.id)
            WHERE students.id = {$this->getId()};");
            $courses = array();
            foreach ($returned_courses as $course )
            {
                $title = $course['title'];
                $id = $course['id'];
                $returned_course = new Course($title, $id);
                array_push($courses, $returned_course);
            }
            return $courses;
        }

        function getDateOfEnrollment($course_id)
        {
            $query = $GLOBALS['DB']->query("SELECT date_of_join FROM courses_students WHERE student_id = {$this->id} AND course_id = {$course_id};");
            $returned_date = $query->fetchAll(PDO::FETCH_ASSOC);
            return $returned_date[0]['date_of_join'];

        }

        //Join Statements NOTE UNTESTED

        function addAccount($account_id)
        {
            $GLOBALS['DB']->exec("INSERT INTO accounts_students (student_id, account_id) VALUES ({$this->getId()}, {$account_id});");
        }

        function addLesson($lesson_id)
        {
            $GLOBALS['DB']->exec("INSERT INTO lessons_students (student_id, lesson_id) VALUES ({$this->getId()}, {$lesson_id});");
        }

        function addService($service_id)
        {
            $GLOBALS['DB']->exec("INSERT INTO service_students (service_id, student_id) VALUES ({$service_id},{$this->getId()})");
        }

        // NOTE TAKING LESSONS REQUIRES A TRIPPLE JOIN TABLE

        function getAccounts()
        {
            $query = $GLOBALS['DB']->query("SELECT accounts.* FROM students JOIN accounts_students ON (students.id = accounts_students.student_id) JOIN accounts ON (accounts_students.account_id = accounts.id) WHERE students.id = {$this->getId()};");
            $accounts = array();
            foreach ($query as $account)
            {
                $id = $account['id'];
                $family_name = $account['family_name'];
                $parent_one_name = $account['parent_one_name'];
                $parent_two_name = $account['parent_two_name'];
                $street_address = $account['street_address'];
                $phone_number = $account['phone_number'];
                $email_address = $account['email_address'];
                $notes = $account['notes'];
                $billing_history = $account['billing_history'];
                $outstanding_balance = intval($account['outstanding_balance']);
                $new_account = new Account($family_name, $parent_one_name,  $street_address, $phone_number, $email_address, $id);
                $new_account->setParentTwoName($parent_two_name);
                $new_account->setNotes($notes);
                $new_account->setBillingHistory($billing_history);
                $new_account->setOutstandingBalance($outstanding_balance);
                array_push($accounts, $new_account);
            }
            return $accounts;
        }

        function getLessons()
        {
            $query = $GLOBALS['DB']->query("SELECT lessons.* FROM students JOIN lessons_students ON (students.id = lessons_students.student_id) JOIN lessons ON (lessons_students.lesson_id = lessons.id) WHERE students.id = {$this->getId()};");
            $lessons = array();
            foreach ($query as $lesson )
            {
                $title = $lesson['title'];
                $description = $lesson['description'];
                $content = $lesson['content'];
                $id = $lesson['id'];
                $returned_lesson = new Lesson($title, $description, $content, $id);
                array_push($lessons, $returned_lesson);
            }
            return $lessons;
        }
    }


 ?>
