<?php
namespace Capstone;


use PDO;
require_once "data/access/Database.php";

class DBResetter
{
    public static function reset(PDO $db)
    {
        // Reset courses
        echo " - Resetting courses...\n";
        $db->query("DELETE FROM courses");
        // Reset assessments
        echo " - Resetting assessments...\n";
        $db->query("DELETE FROM assessments");
        // Reset students
        echo " - Resetting students...\n";
        $db->query("DELETE FROM students");
        // Reset faculty
        echo " - Resetting faculty...\n";
        $db->query("DELETE FROM faculty");
        // Reset addresses
        echo " - Resetting addresses...\n";
        $db->query("DELETE FROM addresses");
        // Reset images
        echo " - Resetting images...\n";
        $db->query("DELETE FROM images");
        // Reset terms
        echo " - Resetting terms...\n";
        $db->query("DELETE FROM terms");
    }
}

if($argc > 1 && $argv[1] == 'reset') {
    echo "Resetting database...\n";
    DBResetter::reset(Database::getInstance());

    echo "... done.\n\n";
} else {
    echo "Missing or incorrect arguments.";
    echo "Usage:\n\tphp DBResetter.php reset\n\n";
}