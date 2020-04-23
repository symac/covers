<?php


namespace App\Service;


class IsbnLibrary
{
    public function clean($isbn) {
        $output = $isbn;
        $output = strtoupper($output);
        $output = preg_replace("/[^\dX]/", "", $output);
        return $output;
    }
}