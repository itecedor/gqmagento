<?php
namespace POSIMWebExt\WebExtManager\Ui\Component\Listing\Column\Posimwebextwebextmanagerinstalledlisting;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if(isset($item["posimwebext_webextmanager_installed_id"]))
                {
                    $id = $item["posimwebext_webextmanager_installed_id"];
                    $webext = strtolower(str_replace('POSIMWebExt_', '', $item["extension"]));
                }
                $item[$name]["view"] = [
                    "href"=>$this->getContext()->getUrl(
                        "webextmanager/update",["ext"=>$webext]),
                    "label"=>__("Update")
                ];
            }
        }

        return $dataSource;
    }    
    
}
