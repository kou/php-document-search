<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    public function scopeFullTextSearch(\Illuminate\Database\Eloquent\Builder $query,
                                        $search_query)
    {
        if ($search_query) {
            return $query->
                select('id',
                       'title',
                       'content',
                       \DB::raw('pgroonga.score(entries) as score'))
                ->whereRaw('title &@ :query OR content &@ :query',
                           ["query" => $search_query])
                ->orderBy('score', 'DESC');
        } else {
            return $query
                ->select('id',
                         'title',
                         'content',
                         /* 'title as highlighted_title', */
                         /* 'content as highlighted_content', */
                         \DB::raw('0 as score'));
        }
    }
}
