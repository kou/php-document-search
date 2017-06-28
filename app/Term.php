<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public function scopeComplete(\Illuminate\Database\Eloquent\Builder $query,
                                  $search_query)
    {
        return $query
            ->select("label")
            /* TODO: Share scopeHighlightHtml */
            ->selectRaw("pgroonga.highlight_html(label, " .
                        "pgroonga.query_extract_keywords(:query_select)) " .
                        "AS highlighted_label",
                        ["query_select" => $search_query])
            ->whereRaw("term &^ :query OR reading &^~ :query OR term @@ :query",
                       ["query" => $search_query])
            ->orderBy("term")
            ->limit(10);
    }
}
