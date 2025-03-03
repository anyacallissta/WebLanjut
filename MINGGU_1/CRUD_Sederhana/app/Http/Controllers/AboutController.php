<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function about() {
        return 'Nama: Anya Callissta Chriswantari <br> NIM: 2341720234';
    }
}