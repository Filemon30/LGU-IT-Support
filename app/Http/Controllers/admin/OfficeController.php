<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OfficeController extends Controller
{
    public function officeDivisions()
    {
        return view('admin.offices.office_divisions');
    }
    
}