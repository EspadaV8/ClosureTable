<?php

namespace EspadaVTest\ClosureTable;

use EspadaVTest\ClosureTable\Models\PageClosure;

class ClosureTableTestCase extends BaseTestCase
{
    /**
     * @var PageClosure;
     */
    protected $ctable;

    /**
     * @var string
     */
    protected $ancestorColumn;

    /**
     * @var string
     */
    protected $descendantColumn;

    /**
     * @var string
     */
    protected $depthColumn;

    public function setUp()
    {
        parent::setUp();

        $this->ctable = new PageClosure;
        $this->ancestorColumn = $this->ctable->getAncestorColumn();
        $this->descendantColumn = $this->ctable->getDescendantColumn();
        $this->depthColumn = $this->ctable->getDepthColumn();
    }

    /**
     * @dataProvider insertNodeProvider
     */
    public function testInsertNodeValidatesItsArguments($ancestorId, $descendantId)
    {
        $this->ctable->insertNode($ancestorId, $descendantId);
    }

    public function insertNodeProvider()
    {
        return [
            ['wrong', 12],
            [12, 'wrong'],
            ['wrong', 'wrong'],
        ];
    }

    public function testMoveNodeToValidatesItsArgument()
    {
        $this->ctable->moveNodeTo('wrong');
    }

    public function testAncestorQualifiedKeyName()
    {
        $this->assertEquals($this->ctable->getTable() . '.' . $this->ancestorColumn, $this->ctable->getQualifiedAncestorColumn());
    }

    public function testDescendantQualifiedKeyName()
    {
        $this->assertEquals($this->ctable->getTable() . '.' . $this->descendantColumn, $this->ctable->getQualifiedDescendantColumn());
    }

    public function testDepthQualifiedKeyName()
    {
        $this->assertEquals($this->ctable->getTable() . '.' . $this->depthColumn, $this->ctable->getQualifiedDepthColumn());
    }
}
