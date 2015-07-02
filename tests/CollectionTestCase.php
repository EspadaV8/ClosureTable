<?php

namespace EspadaVTest\ClosureTable;

use EspadaV8\ClosureTable\Extensions\Collection;
use EspadaVTest\ClosureTable\Models\Page;
use Mockery;

class CollectionTestCase extends BaseTestCase
{
    public function testToTree()
    {
        $rootEntity = new Page;
        $rootEntity->save();
        $childEntity = with(new Page)->moveTo(0, $rootEntity);
        $grandEntity = with(new Page)->moveTo(0, $childEntity);

        $childrenRelationIndex = $rootEntity->getChildrenRelationIndex();

        $tree = with(new Collection([$rootEntity, $childEntity, $grandEntity]))->toTree();
        $rootItem = $tree->get(0);

        $this->assertArrayHasKey($childrenRelationIndex, $rootItem->getRelations());

        $children = $rootItem->getRelation($childrenRelationIndex);

        $this->assertCount(1, $children);

        $childItem = $children->get(0);

        $this->assertEquals($childEntity->getKey(), $childItem->getKey());
        $this->assertArrayHasKey($childrenRelationIndex, $childItem->getRelations());

        $grandItems = $childItem->getRelation($childrenRelationIndex);

        $this->assertCount(1, $grandItems);

        $grandItem = $grandItems->get(0);

        $this->assertEquals($grandEntity->getKey(), $grandItem->getKey());
        $this->assertArrayNotHasKey($childrenRelationIndex, $grandItem->getRelations());
    }

    public function testHasChildren()
    {
        $entity = new Page;
        $childrenRelationIndex = $entity->getChildrenRelationIndex();

        $collection = new Collection([$entity, new Page, new Page]);
        $collection->get(0)->setRelation($childrenRelationIndex, new Collection([new Page, new Page, new Page]));

        $this->assertTrue($collection->hasChildren(0));
    }

    public function testGetChildrenOf()
    {
        $entity = new Page;
        $childrenRelationIndex = $entity->getChildrenRelationIndex();

        $collection = new Collection([$entity, new Page, new Page]);
        $collection->get(0)->setRelation($childrenRelationIndex, new Collection([new Page, new Page, new Page]));

        $children = $collection->getChildrenOf(0);

        $this->assertInstanceOf('EspadaV8\ClosureTable\Extensions\Collection', $children);
        $this->assertCount(3, $children);
    }
}
