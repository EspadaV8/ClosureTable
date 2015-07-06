<?php

namespace EspadaV8\ClosureTable\Models;

use DB;
use EspadaV8\ClosureTable\Contracts\ClosureTableInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Expression;

/**
 * Basic ClosureTable model. Performs actions on the relationships table.
 *
 * @property string ancestor Alias for the ancestor attribute name
 * @property string descendant Alias for the descendant attribute name
 * @property string depth Alias for the depth attribute name
 *
 * @package EspadaV8\ClosureTable
 */
abstract class ClosureTable extends Eloquent implements ClosureTableInterface
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

    /**
     * The column name for the models ancestor id.
     *
     * @var string
     */
    protected $ancestorColumn = 'ancestor';

    /**
     * The column name for the models descendant id.
     *
     * @var string
     */
    protected $descendantColumn = 'descendant';

    /**
     * The column name for the models depth field.
     *
     * @var string
     */
    protected $depthColumn = 'depth';

    /**
     * Inserts new node into closure table.
     *
     * @param string $ancestorId
     * @param string $descendantId
     * @return void
     */
    public function insertNode($ancestorId, $descendantId)
    {
        $ancestor = $this->getAncestorColumn();
        $descendant = $this->getDescendantColumn();
        $depth = $this->getDepthColumn();

        DB::beginTransaction();

        $depthCount = $this->newQuery()
            ->where($descendant, '=', $ancestorId)
            ->first();

        $instance = new static;
        $instance->{$ancestor} = $descendantId;
        $instance->{$descendant} = $descendantId;
        $instance->{$depth} = 0;
        $instance->save();

        if ($depthCount !== null) {
            $instance = new static;
            $instance->{$ancestor} = $depthCount->{$ancestor};
            $instance->{$descendant} = $depthCount->{$descendant};
            $instance->{$depth} = $depthCount->{$depth} + 1;
            $instance->save();
        }

        DB::commit();
    }

    /**
     * Make a node a descendant of another ancestor or makes it a root node.
     *
     * @param string|null $ancestorId
     * @return void
     */
    public function moveNodeTo($ancestorId = null)
    {
        $table = $this->getPrefixedTable();
        $ancestor = $this->getAncestorColumn();
        $descendant = $this->getDescendantColumn();
        $depth = $this->getDepthColumn();

        $thisAncestorId = $this->ancestor;
        $thisDescendantId = $this->descendant;

        // Prevent constraint collision
        if (!is_null($ancestorId) && $thisAncestorId === $ancestorId) {
            return;
        }

        $this->unbindRelationships();

        // Since we have already unbound the node relationships,
        // given null ancestor id, we have nothing else to do,
        // because now the node is already root.
        if (is_null($ancestorId)) {
            return;
        }

        DB::beginTransaction();

        $details = DB::table($table)
            ->join($table . ' as t', new Expression(1), '=', new Expression(1))
            ->select(
                $table . '.' . $ancestor, 't.' . $descendant,
                $table . '.' . $depth . ' as d',
                't.' . $depth
            )
            ->where($table . '.' . $descendant, '=', $ancestorId)
            ->where('t.' . $ancestor, '=', $thisDescendantId)
            ->get()
        ;


        foreach ($details as $row) {
            $instance = new static;
            $instance->{$ancestor} = $row->ancestor;
            $instance->{$descendant} = $row->descendant;
            $instance->{$depth} = $row->d + $row->depth + 1;
            $instance->save();
        }

        DB::commit();
    }

    /**
     * Unbinds current relationships.
     *
     * @return void
     */
    protected function unbindRelationships()
    {
        if ($this->descendant === null) {
            return;
        }

        $ancestorColumn = $this->getAncestorColumn();
        $descendantColumn = $this->getDescendantColumn();
        $descendant = $this->descendant;

        DB::beginTransaction();

        $one = $this->newQuery()
            ->select($descendantColumn)
            ->where($ancestorColumn, '=', $descendant)
            ->lists($descendantColumn);

        $two = $this->newQuery()
            ->select($ancestorColumn)
            ->where($descendantColumn, '=', $descendant)
            ->where($ancestorColumn, '<>', $descendant)
            ->lists($ancestorColumn);

        $this->newQuery()
            ->whereIn($descendantColumn, $one)
            ->whereIn($ancestorColumn, $two)
            ->delete();

        DB::commit();
    }

    /**
     * Get table name with custom prefix for use in raw queries.
     *
     * @return string
     */
    public function getPrefixedTable()
    {
        return DB::getTablePrefix() . $this->getTable();
    }

    /**
     * Get value of the "ancestor" attribute.
     *
     * @return string
     */
    public function getAncestorAttribute()
    {
        return $this->getAttributeFromArray($this->getAncestorColumn());
    }

    /**
     * Set new ancestor id.
     *
     * @param $value
     */
    public function setAncestorAttribute($value)
    {
        $this->attributes[$this->getAncestorColumn()] = $value;
    }

    /**
     * Get the fully qualified "ancestor" column.
     *
     * @return string
     */
    public function getQualifiedAncestorColumn()
    {
        return $this->getTable() . '.' . $this->getAncestorColumn();
    }

    /**
     * Get the short name of the "ancestor" column.
     *
     * @return string
     */
    public function getAncestorColumn()
    {
        return $this->ancestorColumn;
    }

    /**
     * Get value of the "descendant" attribute.
     *
     * @return string
     */
    public function getDescendantAttribute()
    {
        return $this->getAttributeFromArray($this->getDescendantColumn());
    }

    /**
     * Set new descendant id.
     *
     * @param $value
     */
    public function setDescendantAttribute($value)
    {
        $this->attributes[$this->getDescendantColumn()] = $value;
    }

    /**
     * Get the fully qualified "descendant" column.
     *
     * @return string
     */
    public function getQualifiedDescendantColumn()
    {
        return $this->getTable() . '.' . $this->getDescendantColumn();
    }

    /**
     * Get the short name of the "descendant" column.
     *
     * @return string
     */
    public function getDescendantColumn()
    {
        return $this->descendantColumn;
    }

    /**
     * Gets value of the "depth" attribute.
     *
     * @return int
     */
    public function getDepthAttribute()
    {
        return $this->getAttributeFromArray($this->getDepthColumn());
    }

    /**
     * Sets new depth.
     *
     * @param $value
     */
    public function setDepthAttribute($value)
    {
        $this->attributes[$this->getDepthColumn()] = intval($value);
    }

    /**
     * Gets the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDepthColumn()
    {
        return $this->getTable() . '.' . $this->getDepthColumn();
    }

    /**
     * Get the short name of the "depth" column.
     *
     * @return string
     */
    public function getDepthColumn()
    {
        return $this->depthColumn;
    }
}
