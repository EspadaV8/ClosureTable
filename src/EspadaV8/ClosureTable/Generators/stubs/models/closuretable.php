<?php

{{namespace}}

use EspadaV8\ClosureTable\Traits\ClosureTableTrait;
use Illuminate\Database\Eloquent\Model;

class {{closure_class}} extends Model implements {{closure_class}}Interface
{
    use ClosureTableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = {{closure_table}}::class;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'closure_id';
}
