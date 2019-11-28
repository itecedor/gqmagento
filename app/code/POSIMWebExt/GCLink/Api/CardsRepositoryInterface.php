<?php

namespace POSIMWebExt\GCLink\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use POSIMWebExt\GCLink\Api\Data\CardsInterface;

interface CardsRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param string $gcnum
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByGcNum($gcnum);

    /**
     * @param \POSIMWebExt\GCLink\Api\Data\CardsInterface $card
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsInterface
     */
    public function save(CardsInterface $card);

    /**
     * @param \POSIMWebExt\GCLink\Api\Data\CardsInterface $card
     *
     * @return void
     */
    public function delete(CardsInterface $card);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \POSIMWebExt\GCLink\Api\Data\CardsSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
