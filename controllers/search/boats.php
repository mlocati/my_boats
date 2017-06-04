<?php

namespace Concrete\Package\MyBoats\Controller\Search;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Search\StickyRequest;
use MyBoats\Search\Columns\Sets\Boats as ColumnSet;
use MyBoats\Search\Lists\Boats as SearchList;
use MyBoats\Search\Results\Boats as SearchResult;

/**
 * Controller for the boat search.
 */
class Boats extends AbstractController
{
    /**
     * Instance of a class that holds the criteria of the last performed search.
     *
     * @var StickyRequest|null
     */
    private $stickyRequest;

    /**
     * Get the instance of a class that holds the criteria of the last performed search.
     *
     * @return StickyRequest
     */
    public function getStickyRequest()
    {
        if ($this->stickyRequest === null) {
            $this->stickyRequest = $this->app->make(StickyRequest::class, ['myboats.boats']);
        }

        return $this->stickyRequest;
    }

    /**
     * Instance of a class that defines the search list.
     *
     * @var SearchList
     */
    private $searchList;

    /**
     * Get the instance of a class that defines the search list.
     *
     * @return SearchList
     */
    protected function getSearchList()
    {
        if ($this->searchList === null) {
            $this->searchList = $this->app->make(SearchList::class, [$this->getStickyRequest()]);
        }

        return $this->searchList;
    }

    /**
     * Instance of a class that defines the search results.
     *
     * @var SearchResult|null
     */
    private $searchResult;

    /**
     * List of allowed pagination sizes.
     *
     * @return int[]
     */
    public function getAllowedPaginationSizes()
    {
        return [
            10,
            20,
            50,
            100,
            200,
            500,
            1000,
        ];
    }

    /**
     * Get the default pagination size.
     *
     * @return int
     */
    public function getDefaultPaginationSize()
    {
        $allAllowed = $this->getAllowedPaginationSizes();

        return $allAllowed[1];
    }

    /**
     * Perform the search.
     *
     * @param bool $reset Should we reset all the previous search criteria?
     */
    public function search($reset = false)
    {
        $stickyRequest = $this->getStickyRequest();
        $searchList = $this->getSearchList();
        if ($reset) {
            $stickyRequest->resetSearchRequest();
        }
        $req = $stickyRequest->getSearchRequest();

        $columnSet = new ColumnSet();
        if (!$searchList->getActiveSortColumn()) {
            $sortColumn = $columnSet->getDefaultSortColumn();
            $searchList->sanitizedSortBy($sortColumn->getColumnKey(), $sortColumn->getColumnDefaultSortDirection());
        }
        $valn = $this->app->make('helper/validation/numbers');
        /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
        $req = $stickyRequest->getSearchRequest();

        $q = isset($req['enabled']) ? $req['enabled'] : null;
        if ($q === 'yes') {
            $searchList->filterByEnabled(true);
        } elseif ($q === 'no') {
            $searchList->filterByEnabled(false);
        }

        $q = isset($req['name']) ? $req['name'] : null;
        if (is_string($q) && $q !== '') {
            $searchList->filterByName($q);
        }

        $paginationSize = null;
        $q = isset($req['paginationSize']) ? $req['paginationSize'] : null;
        if ($q && $valn->integer($q)) {
            $q = (int) $q;
            $paginationSizes = $this->getAllowedPaginationSizes();
            if (in_array($q, $paginationSizes, true)) {
                $paginationSize = (int) $q;
            }
        }
        if ($paginationSize === null) {
            $paginationSize = $this->getDefaultPaginationSize();
        }
        $searchList->setItemsPerPage($paginationSize);

        $this->searchResult = new SearchResult($columnSet, $searchList, $this->app->make('url/manager')->resolve(['dashboard/boats']));
    }

    /**
     * Get the search result (once the search() method has been called).
     *
     * @return SearchResult|null
     */
    public function getSearchResultObject()
    {
        return $this->searchResult;
    }
}
