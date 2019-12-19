<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Model;

use Magento\InventoryApi\Model\SourceTypeLinkManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\GetSourceTypeLinkInterface;
use Magento\InventoryApi\Api\SourceTypeLinkSaveInterface;
use Magento\InventoryApi\Api\SourceTypeLinkDeleteInterface;

/**
* @inheritdoc
 */
class SourceTypeLinkManagement implements SourceTypeLinkManagementInterface
{

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GetSourceTypeLinkInterface
     */
    private $getSourceTypeLinks;

    /**
     * @var SourceTypeLinkDeleteInterface
     */
    private $commandDelete;

    /**
     * @var SourceTypeLinkSaveInterface
     */
    private $commandSave;

    /**
     * SourceTypeLinkManagement constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GetSourceTypeLinkInterface $getSourceTypeLinks
     * @param SourceTypeLinkSaveInterface $commandSave
     * @param SourceTypeLinkDeleteInterface $commandDelete
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GetSourceTypeLinkInterface $getSourceTypeLinks,
        SourceTypeLinkSaveInterface $commandSave,
        SourceTypeLinkDeleteInterface $commandDelete
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->getSourceTypeLinks = $getSourceTypeLinks;
        $this->commandSave = $commandSave;
        $this->commandDelete = $commandDelete;
    }

    /**
     * @inheritdoc
     */
    public function saveTypeLinksBySource(SourceInterface $source): void
    {
        $this->deleteCurrentTypeLink($source->getSourceCode());
        $this->saveNewTypeLink($source);
    }

    /**
     * @param string $sourceCode
     */
    private function deleteCurrentTypeLink(string $sourceCode)
    {
        $this->commandDelete->execute($sourceCode);
    }

    /**
     * @param SourceInterface $source
     * @return void
     */
    private function saveNewTypeLink(SourceInterface $source)
    {
        $this->commandSave->execute($source);
    }

    /**
     * @inheritdoc
     */
    public function loadTypeLinksBySource(SourceInterface $source): SourceInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceTypeLinkManagementInterface::SOURCE_CODE, $source->getSourceCode())
            ->create();

        $sourceType = $this->getSourceTypeLinks->execute($searchCriteria);

        $sourceTypeFirst = current($sourceType->getItems());

        $extension = $source->getExtensionAttributes();
        $extension->setTypeCode($sourceTypeFirst->getTypeCode());
        $source->setExtensionAttributes($extension);

        return $source;
    }
}
