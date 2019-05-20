<?php
namespace Capstone;


use DateInterval;
use DateTime;

require_once "constants.php";
require_once "data/model/Course.php";
require_once "data/model/Address.php";
require_once "data/model/Person.php";
require_once "data/model/Student.php";
require_once "data/model/Faculty.php";
require_once "data/model/Image.php";
require_once "data/access/Database.php";
require_once "data/access/BaseDao.php";
require_once "data/access/CourseDao.php";
require_once "data/access/AssessmentDao.php";
require_once "data/access/StudentDao.php";
require_once "data/access/FacultyDao.php";
require_once "data/access/AddressDao.php";
require_once "data/access/TermDao.php";

const DOMAIN = 'hku.edu';

class CourseGenerator
{
    public static $course_titles = [
        'Introduction to IT', 'IT Foundations', 'Web Development Fundamentals',
        'Scripting and Programming - Applications', 'Network and Security - Foundations',
        'Data Management - Foundations', 'Data Management for Programmers',
        'Data Management - Applications', 'Operating Systems for Programmers',
        'Business of IT - Project Management', 'Web Development Applications',
        'Software Engineering', 'User Interface Design', 'Software II - Advanced Java Concepts',
        'Business of IT - Applications', 'Software Quality Assurance'
    ];

    public static function generateCourses($count)
    {
        $courses = array();

        for($i=0; $i < $count; $i++)
        {
            $course = self::generateCourse();
            // Add courses to array
            array_push( $courses, $course);
        }

        return $courses;
    }

    public static function generateCourse()
    {
        // Generate random data
        $id = generateUUID();
        $title = getRandomArrayElement(self::$course_titles);
        $course_number = 'C' . rand(100, 1000);
        $credits = rand(3,6);
        $start_date = new DateTime();
        $end_date = getRandomDate($start_date);
        $status = getRandomArrayElement(CourseStatus::getAsArray());

        // Create course
        $course = new Course();
        $course->setCourseId($id);
        $course->setTitle($title);
        $course->setCourseNumber($course_number);
        $course->setCredits($credits);
        $course->setStartDate($start_date);
        $course->setEndDate($end_date);
        $course->setStatus($status);

//        Log::i("Created course: " . $course);

        return $course;
    }
}

class AssessmentGenerator
{
    public static function generateAssessments($count)
    {
        // Container for assessments
        $assessments = array();

        for($i=0; $i < $count; $i++)
        {
            $assessment = self::generateAssessment();
            // Add to collection
            array_push( $assessments, $assessment );
        }

        return $assessments;
    }

    public static function generateAssessment()
    {
        // Generate assessment data
        $id = generateUUID();
        $title = getRandomArrayElement(CourseGenerator::$course_titles);
        $type = getRandomArrayElement(AssessmentType::getAsArray());
        $start_date = new DateTime();
        $end_date = getRandomDate($start_date);

        // Create the assessment
        $assessment = new Assessment($id, $title, $type);
        $assessment->setStartDate($start_date);
        $assessment->setEndDate($end_date);

        return $assessment;
    }
}

class PersonGenerator
{
    public static $first_names = [
        'Gloria', 'Otelia', 'Erlene',
        'Ashlyn', 'Joyce', 'Deedra',
        'Princess', 'Elva', 'Steven',
        'Nenita', 'Tomoko', 'Spring',
        'Brett', 'Charles', 'Matt',
        'Angelo', 'Erick', 'Floy',
        'Normand', 'Marketta', 'Sheri',
        'Eliana', 'Emerson', 'Libby',
        'Tenesha', 'Mana', 'Malcolm',
        'Sheridan', 'Renato', 'Jefferey'
    ];

    public static $last_names = [
        'Abraham', 'Allan', 'Alsop',
        'Butler', 'Cameron', 'Campbell',
        'Carr', 'Chapman', 'Churchill',
        'Clark', 'Clarkson', 'Coleman',
        'Gibson', 'Gill', 'Glover',
        'Graham', 'Grant', 'Gray',
        'Greene', 'Hamilton', 'Hardacre',
        'Johnston', 'Jones', 'Kelly',
        'Kerr', 'King', 'Knox',
        'Mackenzie', 'MacLeod', 'Manning',
        'Marshall', 'Martin', 'Mathis',
        'May', 'Morgan', 'Morrison',
        'Murray', 'Nash', 'Newman', 'Nolan',
        'North', 'Peters', 'Piper', 'Poole',
        'Powell', 'Pullman', 'Quinn',
        'Rampling', 'Randall', 'Rafford', 'Smith',
        'Springer', 'Stewart', 'Sutherland',
        'Taylor', 'Wallace', 'Walsh', 'Watson',
        'Wilkins', 'Wilson', 'Wright', 'Young'
    ];

    public static function generateStudents($count)
    {
        // Container
        $students = array();

        for($i=0; $i < $count; $i++)
        {
            $student =
                self::generatePerson(
                    new Student(null,null,null),
                    self::generatePersonData()
                );
            array_push( $students, $student );
        }

        return $students;
    }

    public static function generateFaculty($count)
    {
        $faculty = array();

        for($i=0; $i < $count; $i++)
        {
            $fac =
                self::generatePerson(
                    new Faculty(null,null,null),
                    self::generatePersonData()
                );
            array_push($faculty, $fac);
        }

        return $faculty;
    }

    public static function generatePersonData()
    {
        // Generate data
        $id = generateUUID();
        $first_name = getRandomArrayElement(self::$first_names);
        $last_name = getRandomArrayElement(self::$last_names);
        $email = strtolower($first_name) . '_' . strtolower($last_name) . '@' . DOMAIN;
        $phone = self::generatePhoneNumber();
        $address = AddressGenerator::generateAddress();
        // Save address
        $address_dao = new AddressDao(Database::getInstance());
        $address_dao->save($address);

        return
            [
                'id' => $id, 'first_name' => $first_name,
                'last_name' => $last_name, 'email' => $email,
                'phone' => $phone, 'address' => $address
            ];
    }

    public static function generatePerson(Person $person, $data)
    {
        // Create person
        $person->setId($data['id']);
        $person->setFirstName($data['first_name']);
        $person->setLastName($data['last_name']);
        $person->setEmail($data['email']);
        $person->setPhone($data['phone']);
        $person->setAddress($data['address']);
        
        return $person;
    }
    
    private static function generatePhoneNumber()
    {
        // Area code
        $area_code = rand(200, 990);
        // Prefix
        $prefix = rand(200, 990);
        // Number
        $number = rand(1000, 100000);

        return
            $area_code . $prefix . $number;
    }
}

class AddressGenerator
{
    private static $streets = [
        'Indian Spring St.', 'Elmwood St.', 'SE. Cleveland Dr.',
        'Prince Street', 'Somerset Ave.', 'Olive Ave.',
        'Amherst Drive', 'Swanson Avenue', 'Hillside Dr.',
        'Ridgewood St.', 'Lantern Street', 'Pulaski Rd.',
        'Lilac Drive', 'SW. Iroquois Ave.', 'Pennsylvania Drive',
        'Broad Circle', 'Arch St.', 'Military Court',
        'Windsor Drive', 'Sunset Rd.', 'Fordham Dr.',
        'South Locust Drive'
    ];

    public static $cities = [
        'Atlantic City', 'Rosedale', 'Middle River',
        'Charlottesville', 'Suffolk', 'Hollywood',
        'Abingdon', 'Milford', 'Pittsburgh', 'Hackensack',
        'Bowie', 'Paterson', 'Ronkonkoma', 'Severna Park',
        'Morganton', 'Fresh Meadows', 'Tualatin', 'Homestead',
        'Oakland', 'Dawsonville'
    ];

    public static $states = [
        'AL', 'AK', 'AZ', 'AR',
        'CA', 'CO', 'CT', 'DE',
        'DC', 'FL', 'GA', 'HA',
        'ID', 'IN', 'IL', 'IA',
        'KS', 'KY', 'LA', 'ME',
        'MD', 'MA', 'MI', 'MS',
        'MN', 'MO', 'MT', 'NE',
        'NV', 'NH', 'NM', 'NY',
        'NC', 'ND', 'OH', 'OK',
        'OR', 'PA', 'RI', 'SC',
        'SD', 'TN', 'TX', 'UT',
        'VT', 'VA', 'WA', 'WV',
        'WI', 'WY'
    ];

    public static function generateAddresses($count)
    {
        $addresses = array();

        for($i=0; $i < $count; $i++)
        {
            $address = self::generateAddress();
            array_push($addresses, $address);
        }

        return $addresses;
    }

    public static function generateAddress()
    {
        $id = generateUUID();
        $building_number = rand(1, 10000);
        $unit_number = rand(1, 100);
        $street = getRandomArrayElement(self::$streets) ;
        $city = getRandomArrayElement(self::$cities);
        $state = getRandomArrayElement(self::$states);
        $country = 'US';
        $post_code = rand(10000, 100000);

        return new Address(
            $id, $building_number, $unit_number, $street, $city,
            $state, $country, $post_code
        );
    }
}

class TermGenerator
{
    public static function generateTerms($count)
    {
        $terms = array();

        for($i=0; $i < $count; $i++) {
            $term = self::generateTerm();
            array_push($terms, $term);
        }

        return $terms;
    }

    private static function generateTerm()
    {
        // Generate data
        $id = generateUUID();
        $title = "Term " . rand(1,7);
        $start = new DateTime();
        $end = getRandomDate();

        // Create term
        $term = new Term();
        $term->setTermId($id);
        $term->setTitle($title);
        $term->setStartDate($start);
        $term->setEndDate($end);

        return $term;
    }
}


class DBPopulator
{
    // I need to:
    // - Generate courses
    // - Generate assessments
    // - Generate students
    // - Generate faculty
    // - Generate addresses
    // - Store all in DB

    const COURSE_COUNT = 10;
    const ASSESSMENT_COUNT = 10;
    const STUDENT_COUNT = 25;
    const FACULTY_COUNT = 10;
    const TERM_COUNT = 7;

    public static function Populate()
    {
        echo " - Generating courses...\n";
        $course_dao = new CourseDao(Database::getInstance());
        $courses = CourseGenerator::generateCourses(self::COURSE_COUNT);
        foreach ($courses as $course) {
            $course_dao->save( $course );
        }

        echo " - Generating assessments...\n";
        $assessment_dao = new AssessmentDao(Database::getInstance());
        $assessments = AssessmentGenerator::generateAssessments(self::ASSESSMENT_COUNT);
        foreach ($assessments as $assessment) {
            $save = $assessment_dao->save($assessment);

            if(!$save) {
                Log::e("Error saving assessment: " . $assessment);
            }
        }

        echo " - Generating students (with addresses)...\n";
        $student_dao = new StudentDao(Database::getInstance());
        $students = PersonGenerator::generateStudents(self::STUDENT_COUNT);
        foreach ($students as $student) {
            $student_dao->save($student);
        }

        echo " - Generating faculty (with addresses)...\n";
        $faculty_dao = new FacultyDao(Database::getInstance());
        $faculty = PersonGenerator::generateFaculty(self::FACULTY_COUNT);
        foreach ($faculty as $staff) {
            $faculty_dao->save($staff);
        }

        echo " - Generating terms...\n";
        $term_dao = new TermDao();
        $terms = TermGenerator::generateTerms(self::TERM_COUNT);
        foreach ($terms as $term) {
            $term_dao->save($term);
        }
    }
}

function generateUUID()
{
    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf(
        '%s%s-%s-%s-%s-%s%s%s',
        str_split( bin2hex($data),4 )
    );
}

function getRandomArrayElement($array)
{
    // Get the size of the array
    $len = count($array);
    // Get a random index
    $index = rand(0, $len -1);

    // Return that element
    return
        $array[$index];
}

function getRandomDate()
{
    // How many days to add
    $interval = rand(0, 30);
    $interval_str = "+{$interval} days";
    $newDate = new DateTime();
    $newDate->modify($interval_str);

    return $newDate;
}


// Run from CLI
if($argc > 1 && $argv[1] == 'auto') {
    echo "Populating database with randomly-generated data...\n";
    DBPopulator::Populate();

    echo "\n\n ... done.\n\n";

} else {
    echo "No arguments provided, exiting...\n\n";
}