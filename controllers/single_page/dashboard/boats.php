<?php

namespace Concrete\Package\MyBoats\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\MyBoats\Controller\Search\Boats as SearchController;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller of the /dashboard/boats page.
 */
class Boats extends DashboardPageController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\DashboardPageController::on_start()
     */    
    public function on_start()
    {
        parent::on_start();
        $this->addHeaderItem(<<<EOT
<style>
table.ccm-search-results-table tr.boat {
    cursor: pointer;
}
table.ccm-search-results-table tr.boat-disabled td {
    background-color: #fee;
}
</style>
EOT
        );
    }

    /**
     * Default method called when viewing the page.
     */
    public function view()
    {
        $resetSearch = false;
        if ($this->request->isPost()) {
            if (!$this->token->validate('myboats-boats-search')) {
                $this->error->add($this->token->getErrorMessage());
            } else {
                $resetSearch = true;
            }
        }
        $searchController = $this->app->make(SearchController::class);
        $searchController->search($resetSearch);
        $result = $searchController->getSearchResultObject();
        $this->set('result', $result);
        $allowedPaginationSizes = array_combine($searchController->getAllowedPaginationSizes(), $searchController->getAllowedPaginationSizes());
        $params = $searchController->getStickyRequest()->getSearchRequest();
        $this->set('name', isset($params['name']) ? $params['name'] : '');
        $this->set('enabled', isset($params['enabled']) ? $params['enabled'] : '');
        if (isset($params['paginationSize']) && isset($allowedPaginationSizes[$params['paginationSize']])) {
            $paginationSize = (int) $params['paginationSize'];
        } else {
            $paginationSize = $searchController->getDefaultPaginationSize();
        }
        $this->set('paginationSize', $paginationSize);
        $this->set('allowedPaginationSizes', $allowedPaginationSizes);
    }
}
