<?php
    namespace App\Controllers;

    class HomeController extends Controller
    {
        public function index($data) {
            self::render('home.index');
        }
    }