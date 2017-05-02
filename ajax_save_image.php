<?php
session_start();
//error_reporting (0);

require_once ('../connect.php');
require_once('../functions_general.php');

$angle  = -90;
$imgName=$_POST['imgName'];

function RotateJpg($filename = '',$angle = 0,$savename = false)
    {
        $original   =   imagecreatefromjpeg($filename);
        $rotated    =   imagerotate($original, $angle, 0);
		echo 			 imagejpeg($rotated,$savename);
		
        
    }

// Base image
$filename   =   '../uploads/'.$imgName;

// Destination, including document root (you may have a defined root to use)
$saveto     =   '../uploads/'.$imgName;

// Apply function
RotateJpg($filename,$angle,$saveto);