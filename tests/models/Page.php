<?php
namespace EspadaV8\ClosureTable\Tests\Models;

use EspadaV8\ClosureTable\Models\Entity;

class Page extends Entity
{
    protected $table = 'entities';
    protected $fillable = ['id', 'title', 'excerpt', 'body', 'position', 'real_depth'];
}
