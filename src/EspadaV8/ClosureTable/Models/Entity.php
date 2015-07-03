<?php

namespace EspadaV8\ClosureTable\Models;

use EspadaV8\ClosureTable\Contracts\EntityInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Basic entity class.
 *
 * Properties, listed below, are used to make the internal code cleaner.
 * However, if you named, for example, the position column to be "pos",
 * remember you can get its value either by $this->pos or $this->position.
 *
 * @property string position Alias for the current position attribute name
 * @property string parent_id Alias for the direct ancestor identifier attribute name
 * @property string real_depth Alias for the real depth attribute name
 *
 * @package EspadaV8\ClosureTable
 */
abstract class Entity extends Model implements EntityInterface
{
    /**
     * ClosureTable model instance.
     *
     * @var ClosureTable
     */
    protected $closure = \EspadaV8\ClosureTable\Models\ClosureTable::class;

    use EntityTrait;
}
