<?php

namespace Concrete\Package\MyBoats;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManager;
use MyBoats\Entity\Boat;

/**
 * The package controller.
 *
 * Manages the package installation, update and start-up.
 */
class Controller extends Package
{
    /**
     * The minimum concrete5 version.
     *
     * @var string
     */
    protected $appVersionRequired = '8';

    /**
     * The unique handle that identifies the package.
     *
     * @var string
     */
    protected $pkgHandle = 'my_boats';

    /**
     * The package version.
     *
     * @var string
     */
    protected $pkgVersion = '1.0.0';

    /**
     * Map folders to PHP namespaces, for automatic class autoloading.
     *
     * @var array
     */
    protected $pkgAutoloaderRegistries = [
        'src' => 'MyBoats',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageName()
     */
    public function getPackageName()
    {
        return t('My Boats');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageDescription()
     */
    public function getPackageDescription()
    {
        return t('Sample package to show the power of ItemLists');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::install()
     */
    public function install()
    {
        $pkg = parent::install();
        $this->installXml();
        $this->addInitialBoats();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::upgrade()
     */
    public function upgrade()
    {
        parent::upgrade();
        $this->installXml();
    }

    /**
     * Install/update data from install XML file.
     */
    private function installXml()
    {
        $contentImporter = $this->app->make(ContentImporter::class);
        $contentImporter->importContentFile($this->getPackagePath() . '/install.xml');
    }

    /**
     * Add some sample boats.
     */
    private function addInitialBoats()
    {
        $em = $this->app->make(EntityManager::class);
        /* @var EntityManager $em */
        $repo = $em->getRepository(Boat::class);
        $r = $repo->createQueryBuilder('b')->select('b.id')->setMaxResults(1)->getQuery()->execute();
        if (empty($r)) {
            foreach ([
                Boat::create('My Boat Of Unknown Length', true),
                Boat::create('My Tiny Boat', false, 1.5),
                Boat::create('My Medium Boat', true, 5),
                Boat::create('My Big Boat', true, 15),
                Boat::create('My Huge Boat', false, 100),
                Boat::create('Titanic', true, 209),
            ] as $boat) {
                $em->persist($boat);
            }
            $em->flush();
        }
    }
}
