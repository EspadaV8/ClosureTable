<?php

namespace EspadaVTest\ClosureTable\Models;

use EspadaV8\ClosureTable\Models\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Entity
{
    use SoftDeletes;

    public $timestamps = false;

    /**
     * ClosureTable model instance.
     *
     * @var ClosureTable
     */
    protected $closure = \EspadaVTest\ClosureTable\Models\PageClosure::class;

    protected $table = 'entities';

    protected $fillable = ['id', 'title', 'excerpt', 'body', 'position', 'real_depth'];
}
