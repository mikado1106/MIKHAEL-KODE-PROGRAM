<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DatabaseTest extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        if ($db->connect()) {
            echo "Database Connected!";
        } else {
            echo "Database Connection Failed.";
        }
    }
}
