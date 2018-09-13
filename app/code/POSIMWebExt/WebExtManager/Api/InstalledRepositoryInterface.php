<?php
namespace POSIMWebExt\WebExtManager\Api;

use POSIMWebExt\WebExtManager\Model\InstalledInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface InstalledRepositoryInterface 
{
    public function save(InstalledInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(InstalledInterface $page);

    public function deleteById($id);
}
