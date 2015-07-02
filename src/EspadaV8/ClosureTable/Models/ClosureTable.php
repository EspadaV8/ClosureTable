<?php

namespace EspadaV8\ClosureTable\Models;

use DB;
use EspadaV8\ClosureTable\Contracts\ClosureTableInterface;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Basic ClosureTable model. Performs actions on the relationships table.
 *
 * @property string ancestor Alias for the ancestor attribute name
 * @property string descendant Alias for the descendant attribute name
 * @property string depth Alias for the depth attribute name
 *
 * @package EspadaV8\ClosureTable
 */
class ClosureTable extends Eloquent implements ClosureTableInterface
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Inserts new node into closure table.
     *
     * @param string $ancestorId
     * @param string $descendantId
     * @return void
     */
    public function insertNode($ancestorId, $descendantId)
    {
        $table = $this->getPrefixedTable();
        $ancestor = $this->getAncestorColumn();
        $descendant = $this->getDescendantColumn();
        $depth = $this->getDepthColumn();

        $query = "
            INSERT INTO {$table} ({$ancestor}, {$descendant}, {$depth})
            SELECT tbl.{$ancestor}, ?, tbl.{$depth}+1
            FROM {$table} AS tbl
            WHERE tbl.{$descendant} = ?
            UNION ALL
            SELECT ?, ?, 0
        ";

        $bindings = [
            $descendantId,
            $ancestorId,
            $descendantId,
            $descendantId,
        ];

        DB::statement($query, $bindings);
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

        $query = "
            INSERT INTO {$table} ({$ancestor}, {$descendant}, {$depth})
            SELECT supertbl.{$ancestor}, subtbl.{$descendant}, supertbl.{$depth}+subtbl.{$depth}+1
            FROM {$table} as supertbl
            CROSS JOIN {$table} as subtbl
            WHERE supertbl.{$descendant} = ?
            AND subtbl.{$ancestor} = ?
        ";

        $bindings = [
            $ancestorId,
            $thisDescendantId,
        ];

        DB::statement($query, $bindings);
    }

    /**
     * Unbinds current relationships.
     *
     * @return void
     */
    protected function unbindRelationships()
    {
        $table = $this->getPrefixedTable();
        $ancestorColumn = $this->getAncestorColumn();
        $descendantColumn = $this->getDescendantColumn();
        $descendant = $this->descendant;

        $query = "
            DELETE FROM {$table}
            WHERE {$descendantColumn} IN (
              SELECT d FROM (
                SELECT {$descendantColumn} as d FROM {$table}
                WHERE {$ancestorColumn} = ?
              ) as dct
            )
            AND {$ancestorColumn} IN (
              SELECT a FROM (
                SELECT {$ancestorColumn} AS a FROM {$table}
                WHERE {$descendantColumn} = ?
                AND {$ancestorColumn} <> ?
              ) as ct
            )
        ";

        $bindings = [
            $descendant,
            $descendant,
            $descendant,
        ];

        DB::delete($query, $bindings);
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
        return 'ancestor';
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
        return 'descendant';
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
        return 'depth';
    }
}
