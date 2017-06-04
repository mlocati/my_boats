<?php

namespace MyBoats\Search\Results;

use Concrete\Core\Search\Result\Result as SearchResult;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;
use MyBoats\Entity\Boat as BoatEntity;
use Pagerfanta\View\TwitterBootstrap3View;

/**
 * Class that contains the results of the boat searches.
 */
class Boats extends SearchResult
{
    /**
     * @var EntityManager|null
     */
    private $entityManager;

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ($this->entityManager === null) {
            $this->entityManager = Application::getFacadeApplication()->make(EntityManager::class);
        }

        return $this->entityManager;
    }

    public function getItemDetails($boat)
    {
        return new Item\Boats($this, $this->listColumns, $boat);
    }

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
