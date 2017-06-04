<?php

namespace MyBoats\Search\Lists;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\Pagination\Pagination;
use Doctrine\ORM\EntityManager;
use MyBoats\Entity\Boat;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

/**
 * Class that manages the criterias of the boat searches.
 */
class Boats extends ItemList implements ApplicationAwareInterface
{
    /**
     * The application container.
     *
     * @var Application
     */
    protected $app;

    /**
     * {@inheritdoc}
     *
     * @see ApplicationAwareInterface::setApplication()
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * The parameter name to be used for pagination.
     *
     * @var string
     */
    protected $paginationPageParameter = 'boats_page';

    /**
     * The columns that can be sorted via the web interface.
     *
     * @var array
     */
    protected $autoSortColumns = [
        'b.name',
        'b.enabled',
        'b.length',
    ];

    /**
     * {@inheritdoc}
     *
     * @see ItemList::createQuery()
     */
    public function createQuery()
    {
        $this->query->select('b.id')
            ->from('Boats', 'b')
        ;
    }

    /**
     * Filter the results by part of the boat name.
     *
     * @param string $name
     */
    public function filterByName($name)
    {
        $name = (string) $name;
        if ($name !== '') {
            $this->query->andWhere($this->query->expr()->like('b.name', $this->query->createNamedParameter('%' . addcslashes($name, '%_\\') . '%')));
        }
    }

    /**
     * Filter the enabled/disabled boats.
     *
     * @param bool $enabled
     */
    public function filterByEnabled($enabled)
    {
        $this->query->andWhere($this->query->expr()->eq('b.enabled', $enabled ? 1 : 0));
    }

    /**
     * Include in the results only the boats without a length.
     */
    public function filterByWithoutLength()
    {
        $this->query->andWhere($this->query->expr()->isNull('b.length'));
    }

    /**
     * Include in the results only the boats with a length.
     */
    public function filterByWithLength()
    {
        $this->query->andWhere($this->query->expr()->isNotNull('b.length'));
    }

    /**
     * Include in the results only the boats with aspecified minimum length.
     *
     * @param mixed $value
     */
    public function filterByMinimumLength($value)
    {
        $this->query->andWhere($this->query->expr()->andX()
            ->add($this->query->expr()->isNotNull('b.length'))
            ->add($this->query->expr()->gte('b.length', $this->query->createNamedParameter($value)))
        );
    }

    /**
     * Include in the results only the boats with aspecified minimum length.
     *
     * @param mixed $value
     */
    public function filterByMaximumLength($value)
    {
        $this->query->andWhere($this->query->expr()->andX()
            ->add($this->query->expr()->isNotNull('b.length'))
            ->add($this->query->expr()->lte('b.length', $this->query->createNamedParameter($value)))
            );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\ItemList\ItemList::getTotalResults()
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        $query
            ->resetQueryParts(['groupBy', 'orderBy'])
            ->select('count(distinct b.id)')
            ->setMaxResults(1);
        $result = $query->execute()->fetchColumn();

        return (int) $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\ItemList\ItemList::createPaginationObject()
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter(
            $this->deliverQueryObject(),
            function (\Doctrine\DBAL\Query\QueryBuilder $query) {
                $query
                    ->resetQueryParts(['groupBy', 'orderBy'])
                    ->select('count(distinct b.id)')
                    ->setMaxResults(1);
            }
        );
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Search\ItemList\ItemList::getResult()
     */
    public function getResult($queryRow)
    {
        $entityManager = $this->app->make(EntityManager::class);
        /* @var EntityManager $entityManager */
        return $entityManager->find(Boat::class, $queryRow['id']);
    }
}
