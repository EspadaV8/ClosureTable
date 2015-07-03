<?php

namespace EspadaV8\ClosureTable\Models;

use EspadaV8\ClosureTable\Contracts\ClosureTableInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Basic ClosureTable model. Performs actions on the relationships table.
 *
 * @property string ancestor Alias for the ancestor attribute name
 * @property string descendant Alias for the descendant attribute name
 * @property string depth Alias for the depth attribute name
 *
 * @package EspadaV8\ClosureTable
 */
abstract class ClosureTable extends Model implements ClosureTableInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entities_closure';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'closure_id';

    use ClosureTableTrait;
}
