<?php

namespace MyBoats\Search\Results;

use Concrete\Core\Search\Result\Result as SearchResult;
use Pagerfanta\View\TwitterBootstrap3View;

/**
 * Class that contains the results of the boat searches.
 */
class Boats extends SearchResult
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\Result\Result::getItemDetails()
     */
    public function getItemDetails($boat)
    {
        return new Item\Boats($this, $this->listColumns, $boat);
    }

    /**
     * Builds the HTML to be used to control the pagination.
     *
     * @return string
     */
    public function getPaginationHTML()
    {
        if ($this->pagination->haveToPaginate()) {
            $view = new TwitterBootstrap3View();
            $me = $this;
            $result = $view->render(
                $this->pagination,
                function ($page) use ($me) {
                    $list = $me->getItemListObject();
                    $result = (string) $me->getBaseURL();
                    $result .= strpos($result, '?') === false ? '?' : '&';
                    $result .= ltrim($list->getQueryPaginationPageParameter(), '&') . '=' . $page;

                    return $result;
                },
                [
                    'prev_message' => tc('Pagination', '&larr; Previous'),
                    'next_message' => tc('Pagination', 'Next &rarr;'),
                    'active_suffix' => '<span class="sr-only">' . tc('Pagination', '(current)') . '</span>',
                ]
            );
        } else {
            $result = '';
        }

        return $result;
    }
}
