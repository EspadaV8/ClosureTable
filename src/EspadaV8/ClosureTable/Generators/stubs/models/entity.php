<?php

{{namespace}}

use EspadaV8\ClosureTable\Traits\EntityTrait;
use Illuminate\Database\Eloquent\Model;

class {{entity_class}} extends Model implements {{entity_class}}Interface
{
    use EntityTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = '{{entity_table}}';

    /**
     * ClosureTable model instance.
     *
     * @var {{closure_class_short}}
     */
    protected $closure = {{closure_class}}::class;
}
