<?php

namespace App\Http\Controllers;

use App\Entry;
use Illuminate\Http\Request;

class EntrySearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $entries = Entry::all();
        return view('entry.search.index', ['entries' => $entries]);
    }
}
