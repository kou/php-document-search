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
                ->select('id', 'url', 'title', 'content')
                ->selectRaw('pgroonga_score(entries) AS score')
                ->highlightHTML('title', $search_query)
                ->snippetHTML('content', $search_query)
                ->whereRaw("title &@~ pgroonga_query_expand(?, ?, ?, ?) OR " .
                           "content &@~ pgroonga_query_expand(?, ?, ?, ?)",
                           ["synonyms", "term", "synonym", ">({$search_query})",
                            "synonyms", "term", "synonym", $search_query])
                ->orderBy('score', 'DESC');
        } else {
            return $query
                ->select('id', 'url', 'title', 'content')
                ->selectRaw('0 AS score')
                ->highlightHTML('title', null)
                ->snippetHTML('content', null)
                ->orderBy('id');
        }
    }

    public function scopeHighlightHTML(\Illuminate\Database\Eloquent\Builder $query,
                                       $column,
                                       $search_query)
    {
        if ($search_query) {
            return $query
                ->selectRaw("pgroonga_highlight_html($column, " .
                            "pgroonga_query_extract_keywords(" .
                            "pgroonga_query_expand(?, ?, ?, ?))) " .
                            "AS highlighted_$column",
                            ["synonyms", "term", "synonym", $search_query]);
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
                ->selectRaw("pgroonga_snippet_html($column, " .
                            "pgroonga_query_extract_keywords( " .
                            "pgroonga_query_expand(?, ?, ?, ?))) " .
                            "AS {$column}_snippets",
                            ["synonyms", "term", "synonym", $search_query]);
        } else {
            return $query
                ->selectRaw("ARRAY[]::text[] AS {$column}_snippets");
        }
    }

    public function getContentSnippetsAttribute($value)
    {
        return array_map(function ($element) {
                             return preg_replace('/\\\\(.)/', '$1', $element);
                         },
                         explode('","', substr($value, 2, -2)));
    }

    public function scopeSimilarSearch(\Illuminate\Database\Eloquent\Builder $query,
                                       $text)
    {
        return $query
            ->select('id', 'url', 'title')
            ->selectRaw('pgroonga_score(entries) AS score')
            ->whereRaw("(title || ' ' || content) &@* ?",
                       [$text])
            ->orderBy('score', 'DESC');
    }

    public function similarEntries()
    {
        return Entry::query()
            ->similarSearch("{$this->title} {$this->content}")
            ->where("id", "<>", $this->id)
            ->limit(3)
            ->get();
    }
}
