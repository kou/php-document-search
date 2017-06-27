<?php

namespace App\Http\Controllers;

use App\Term;
use Illuminate\Http\Request;

class TermSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request["query"];
        $terms = Term::select("label")
            ->whereRaw("term &^ :query OR reading &^~ :query",
                       ["query" => $query])
            ->orderBy("term")
            ->limit(10)
            ->cursor();
        $data = [];
        foreach ($terms as $term) {
            $data[] = $term->label;
        }
        return response()->json($data);
    }
}
