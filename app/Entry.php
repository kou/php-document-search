<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    public function scopeFullTextSearch(\Illuminate\Database\Eloquent\Builder $query,
                                        $search_query)
    {
        if ($search_query) {
            return $query
                ->select('id',
                       'url',
                       \DB::raw('pgroonga.score(entries) AS score'))
                ->highlightHTML('title', $search_query)
                ->snippetHTML('content', $search_query)
                ->whereRaw('title @@ ? OR content @@ ?',
                           [$search_query, $search_query])
                ->orderBy('score', 'DESC');
        } else {
            return $query
                ->select('id',
                         'url',
                         \DB::raw('0 AS score'))
                ->highlightHTML('title', null)
                ->snippetHTML('content', null);
        }
    }

    public function scopeHighlightHTML(\Illuminate\Database\Eloquent\Builder $query,
                                       $column,
                                       $search_query)
    {
        if ($search_query) {
            return $query
                ->selectRaw("pgroonga.highlight_html($column, " .
                            "pgroonga.query_extract_keywords(?)) " .
                            "AS highlighted_$column",
                            [$search_query]);
        } else {
            return $query
                ->selectRaw("pgroonga.highlight_html($column, " .
                            "ARRAY[]::text[]) " .
                            "AS highlighted_$column");
        }
    }


    public function scopeSnippetHTML(\Illuminate\Database\Eloquent\Builder $query,
                                     $column,
                                     $search_query)
    {
        if ($search_query) {
            return $query
                ->selectRaw("pgroonga.snippet_html($column, " .
                            "pgroonga.query_extract_keywords(?)) " .
                            "AS {$column}_snippets",
                            [$search_query]);
        } else {
            return $query
                ->selectRaw("ARRAY[]::text[] AS {$column}_snippets");
        }
    }

    public function arrayValue($column)
    {
        return array_map(function ($element) {
                             return preg_replace('/\\\\(.)/', '$1', $element);
                         },
                         explode('","', substr($this->$column, 2, -2)));
    }
}
