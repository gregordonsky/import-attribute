<?php

/**
 * TechDivision\Import\Attribute\Observers\AttributeOptionExportObserver
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

/**
 * Observer that exports the attribute options to an additional CSV file for further processing.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-attribute
 * @link      http://www.techdivision.com
 */
class AttributeOptionExportObserver extends AbstractAttributeExportObserver
{

    /**
     * The artefact type.
     *
     * @var string
     */
    const ARTEFACT_TYPE = 'option-import';

    /**
     * Process the observer's business logic.
     *
     * @return void
     */
    protected function process()
    {

        // do NOT export the value if we're NOT in the admin store view
        if (!$this->isAdminStore()) {
            return;
        }

        // initialize the array with the artefacts
        $artefacts = array();

        // load the attribute option values/positions
        $attributeOptionValues = $this->getValue(ColumnKeys::ATTRIBUTE_OPTION_VALUES, array(), array($this, 'explode'));
        $attributeOptionPositions = $this->getValue(ColumnKeys::ATTRIBUTE_OPTION_POSITIONS, array(), array($this, 'explode'));
        $attributeOptionSwatch = $this->explode($this->getValue(ColumnKeys::ATTRIBUTE_OPTION_SWATCH), $this->getMultipleValueDelimiter());

        // iterate over the attribute option values and export them
        foreach ($attributeOptionValues as $key => $attributeOptionValue) {
            // initialize the attribute option swatch data
            $optionSwatch = array();
            if (isset($attributeOptionSwatch[$key])) {
                foreach ($this->explode($attributeOptionSwatch[$key]) as $value)  {
                    $explodedSwatch = $this->explode($value, '=');
                    if (isset($explodedSwatch[0]) && isset($explodedSwatch[1])) {
                        $optionSwatch[$explodedSwatch[0]] = $explodedSwatch[1];
                    }
                }
            }

            // initialize and add the new artefact
            $artefacts[] = $this->newArtefact(
                array(
                    ColumnKeys::ATTRIBUTE_CODE  => $this->getValue(ColumnKeys::ATTRIBUTE_CODE),
                    ColumnKeys::VALUE           => $attributeOptionValue,
                    ColumnKeys::POSITION        => isset($attributeOptionPositions[$key]) ? $attributeOptionPositions[$key] : 0,
                    ColumnKeys::SWATCH_TYPE     => isset($optionSwatch[ColumnKeys::TYPE]) ? $optionSwatch[ColumnKeys::TYPE] : null,
                    ColumnKeys::SWATCH_VALUE    => isset($optionSwatch[ColumnKeys::VALUE]) ? $optionSwatch[ColumnKeys::VALUE] : null
                ),
                array(
                    ColumnKeys::ATTRIBUTE_CODE  => ColumnKeys::ATTRIBUTE_CODE,
                    ColumnKeys::VALUE           => ColumnKeys::VALUE,
                    ColumnKeys::POSITION        => ColumnKeys::POSITION,
                    ColumnKeys::SWATCH_TYPE     => ColumnKeys::ATTRIBUTE_OPTION_SWATCH,
                    ColumnKeys::SWATCH_VALUE    => ColumnKeys::ATTRIBUTE_OPTION_SWATCH
                )
            );
        }

        // add the array with the artefacts
        $this->addArtefacts($artefacts);
    }

    /**
     * Return's the artefact type used for export.
     *
     * @return string The artefact type
     */
    protected function getArtefactType()
    {
        return AttributeOptionExportObserver::ARTEFACT_TYPE;
    }
}
