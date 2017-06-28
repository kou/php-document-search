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
        $terms = Term::query()->complete($query);
        $data = [];
        foreach ($terms->get() as $term) {
            $data[] = [
                "value" => $term->label,
                "label" => $term->highlighted_label,
            ];
        }
        return response()->json($data);
    }
}
