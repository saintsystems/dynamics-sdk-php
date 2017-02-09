<?php

use PHPUnit\Framework\TestCase;
use Microsoft\OData;
use Microsoft\Dynamics\Model;

class ModelTest extends TestCase
{
    private $entities;
    private $enums;
    private $complexTypes;

    public function setUp()
    {
        $this->entities = array();
        $this->enums = array();
        $this->complexTypes = array();

        $dir = new DirectoryIterator('src/Dynamics/Model');
        foreach ($dir as $fileInfo)
        {
            $filename = $fileInfo->getFileName();
            $classname = explode(".", $filename)[0];
            if ($classname != null) {
                $class = "Microsoft\\Dynamics\\Model\\" . explode(".", $fileInfo->getFileName())[0];
                switch(get_parent_class($class)) {
                    case OData\Entity::class:
                        $this->entities[] = $class;
                        break;
                    case OData\Enum::class:
                        $this->enums[] = $class;
                        break;
                    default:
                        $this->complexTypes[] = $class;
                        break;
                }
            }
        }
    }

    public function testBaseEntity()
    {
        $entity = new OData\Entity();
        $this->assertInstanceOf(OData\Entity::class, $entity);
    }

    public function testEntity()
    {
        foreach ($this->entities as $entityClass) {
            $entity = new $entityClass();
            $this->assertInstanceOf($entityClass, $entity);
        }
    }

    public function testEntityNames()
    {
        foreach ($this->entities as $entityClass) {
            $entity = new $entityClass();
            $primaryKey = $entity::$primaryKey;
            $expected = $entity->getKeyName();
            $this->assertEquals($expected, $primaryKey);
        }
    }

    public function testComplexTypes()
    {
        foreach ($this->complexTypes as $complexTypeClass) {
            $complexEntity = new $complexTypeClass();
            $this->assertInstanceOf($complexTypeClass, $complexEntity);
        }
    }

    public function testLeadEntity()
    {
        $lead = new Model\Lead([
            'leadid' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'firstname' => 'Bob',
            'lastname' => 'Barker',
            'address1_city' => 'Somewhere',
        ]);
        $this->assertEquals('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx', $lead->id);
        $this->assertEquals('Bob', $lead->firstname);
        $this->assertEquals('Barker', $lead->lastname);
        $this->assertEquals('Somewhere', $lead->address1_city);
    }

    // public function testLeadEntityWithMutator()
    // {
    //     $lead = new Model\Lead([
    //         'firstname' => 'Bob',
    //         'lastname' => 'Barker',
    //     ]);
    //     $this->assertEquals('Bob Barker', $lead->fullname);
    // }
}
