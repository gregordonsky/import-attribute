<?php

/**
 * TechDivision\Import\Attribute\Observers\CatalogAttributeObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Attribute\Observers;

use TechDivision\Import\Attribute\Utils\ColumnKeys;
use TechDivision\Import\Attribute\Utils\MemberNames;
use TechDivision\Import\Utils\EntityStatus;

/**
 * Test class for the catalog attribute observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */
class CatalogAttributeObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The observer we want to test.
     *
     * @var \TechDivision\Import\Attribute\Observers\CatalogAttributeObserver
     */
    protected $observer;

    /**
     * The mock bunch processor instance.
     *
     * @var \TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface
     */
    protected $mockBunchProcessor;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // mock the attribute bunch processor
        $this->mockBunchProcessor = $this->getMockBuilder('TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface')
                                         ->setMethods(get_class_methods('TechDivision\Import\Attribute\Services\AttributeBunchProcessorInterface'))
                                         ->getMock();

        // the observer instance we want to test
        $this->observer = new CatalogAttributeObserver($this->mockBunchProcessor);
    }

    /**
     * Test's the handle() method successfull.
     *
     * @return void
     */
    public function testHandleWithoutAnyFields()
    {

        // create a dummy CSV file row
        $row = array(
            0  => $attributeCode = 'test_attribute_code'
        );

        // create a mock subject instance
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Attribute\Observers\AttributeSubjectImpl')
                            ->setMethods(get_class_methods('TechDivision\Import\Attribute\Observers\AttributeSubjectImpl'))
                            ->getMock();
        $mockSubject->expects($this->once())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->exactly(24))
                    ->method('hasHeader')
                    ->withConsecutive(
                        array(ColumnKeys::ATTRIBUTE_CODE),
                        array(ColumnKeys::FRONTEND_INPUT_RENDERER),
                        array(ColumnKeys::IS_GLOBAL),
                        array(ColumnKeys::IS_VISIBLE),
                        array(ColumnKeys::IS_SEARCHABLE),
                        array(ColumnKeys::IS_FILTERABLE),
                        array(ColumnKeys::IS_COMPARABLE),
                        array(ColumnKeys::IS_VISIBLE_ON_FRONT),
                        array(ColumnKeys::IS_HTML_ALLOWED_ON_FRONT),
                        array(ColumnKeys::IS_USED_FOR_PRICE_RULES),
                        array(ColumnKeys::IS_FILTERABLE_IN_SEARCH),
                        array(ColumnKeys::USED_IN_PRODUCT_LISTING),
                        array(ColumnKeys::USED_FOR_SORT_BY),
                        array(ColumnKeys::APPLY_TO),
                        array(ColumnKeys::IS_VISIBLE_IN_ADVANCED_SEARCH),
                        array(ColumnKeys::POSITION),
                        array(ColumnKeys::IS_WYSIWYG_ENABLED),
                        array(ColumnKeys::IS_USED_FOR_PROMO_RULES),
                        array(ColumnKeys::IS_REQUIRED_IN_ADMIN_STORE),
                        array(ColumnKeys::IS_USED_IN_GRID),
                        array(ColumnKeys::IS_VISIBLE_IN_GRID),
                        array(ColumnKeys::IS_FILTERABLE_IN_GRID),
                        array(ColumnKeys::SEARCH_WEIGHT),
                        array(ColumnKeys::ADDITIONAL_DATA)
                     )
                    ->willReturnOnConsecutiveCalls(
                        true,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                    );
        $mockSubject->expects($this->once())
                    ->method('getHeader')
                    ->with(ColumnKeys::ATTRIBUTE_CODE)
                    ->willReturn(0);
        $mockSubject->expects($this->once())
                    ->method('hasBeenProcessed')
                    ->with($attributeCode)
                    ->willReturn(false);
        $mockSubject->expects($this->once())
                    ->method('getLastAttributeId')
                    ->willReturn($lastAttributeId = 1001);

        // initialize the expected entity that should be persisted
        $expectedEntity = array(
            MemberNames::ATTRIBUTE_ID                  => $lastAttributeId,
            EntityStatus::MEMBER_NAME                  => EntityStatus::STATUS_CREATE
        );

        // mock the method that persists the entity
        $this->mockBunchProcessor->expects($this->once())
                                 ->method('persistCatalogAttribute')
                                 ->with($expectedEntity)
                                 ->willReturn(null);

        // invoke the handle method
        $this->assertSame($row, $this->observer->handle($mockSubject));
    }
}
