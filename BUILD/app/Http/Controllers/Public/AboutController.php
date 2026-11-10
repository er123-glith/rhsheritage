<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PageContent;

class AboutController extends Controller
{
    public function index()
    {
        $content = PageContent::where('key', 'tentang')->first();
        return view('public.about', compact('content'));
    }
}
