<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Download;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::active()->get();
        return view('downloads.index', compact('downloads'));
    }
}

