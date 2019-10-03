<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Model\ResourceModel\SourceTypeLink;

use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\ResourceModel\StockSourceLink as StockSourceLinkResourceModel;
use Magento\Inventory\Model\ResourceModel\SourceTypeLinkFactory as SourceTypeLinkResourceModelFactory;
use Magento\InventoryApi\Api\Data\StockSourceLinkInterface;

/**
 * Implementation of StockSourceLink delete multiple operation for specific db layer
 * Delete Multiple used here for performance efficient purposes over single delete operation
 */
class DeleteMultiple
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    private $soureTypeLinkFactory;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        SourceTypeLinkResourceModelFactory $sourceTypeLinkFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->soureTypeLinkFactory = $sourceTypeLinkFactory;
    }

    /**
     * Multiple delete stock source links
     *
     * @param StockSourceLinkInterface[] $links
     * @return void
     */
    public function execute(array $links)
    {
        if (!count($links)) {
            return;
        }

//        $sourceTypeLinkResourceModel = $this->SourceTypeLinkResourceModelFactory->create();

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(
            "inventory_source_type_link"
        );

//        var_dump($tableName);
//        die;

        $whereSql = $this->buildWhereSqlPart($links);
        $connection->delete($tableName, $whereSql);
    }

    /**
     * Build WHERE part of the delete SQL query
     *
     * @param StockSourceLinkInterface[] $links
     * @return string
     */
    private function buildWhereSqlPart(array $links): string
    {
        $connection = $this->resourceConnection->getConnection();

        $condition = [];

        foreach ($links as $link) {
            $skuCondition = $connection->quoteInto(
                StockSourceLinkInterface::SOURCE_CODE . ' = ?',
                $link->getSourceCode()
            );
            $sourceCodeCondition = $connection->quoteInto(
                StockSourceLinkInterface::STOCK_ID . ' = ?',
                $link->getStockId()
            );
            $condition[] = '(' . $skuCondition . ' AND ' . $sourceCodeCondition . ')';
        }

        return implode(' OR ', $condition);
    }
}
