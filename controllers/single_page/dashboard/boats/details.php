<?php

namespace Concrete\Package\MyBoats\Controller\SinglePage\Dashboard\Boats;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use MyBoats\Entity\Boat;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Controller of the /dashboard/boats/details page.
 */
class Details extends DashboardPageController
{
    /**
     * Default method called when viewing the page.
     *
     * @param mixed $id
     */
    public function view($id = null)
    {
        $boat = $this->idToEntity($id);
        if ($boat === null) {
            $this->flash('error', t('Unable to find the specified boat.'));

            return $this->redirect('/dashboard/boats');
        }
        $this->set('boat', $boat);
    }

    /**
     * Method called when saving the entity.
     *
     * @param mixed $id
     */
    public function save($id = null)
    {
        $entity = $this->idToEntity($id);
        if ($entity === null) {
            $this->flash('error', t('Unable to find the specified boat.'));

            return $this->redirect('/dashboard/boats');
        }
        $errors = $this->app->make('error');
        /* @var \Concrete\Core\Error\ErrorList\ErrorList $errors */
        if (!$this->token->validate('myboats-boats-details-' . $id)) {
            $errors->add($this->token->getErrorMessage());
        } else {
            $valn = $this->app->make('helper/validation/numbers');
            /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
            $post = $this->request->request;
            $value = $post->get('name');
            $value = is_string($value) ? trim($value) : '';
            if ($value === '') {
                $errors->add(t('Please specify the name of the boat.'));
            } else {
                $entity->setName($value);
            }
            $value = $post->get('enabled');
            if ($valn->integer($value, 0, 1)) {
                $entity->setIsEnabled($value);
            } else {
                $errors->add(t('Please specify if the boat is enabled.'));
            }
            $value = $post->get('length');
            if ($value === null || $value === '') {
                $entity->setLength(null);
            } elseif ($valn->integer($value, 1)) {
                $entity->setLength((int) $value);
            } else {
                $errors->add(t('Invalid boat length specified.'));
            }
        }
        if ($errors->has()) {
            $this->view($id);
            $this->flash('error', $errors);
        } else {
            $this->entityManager->persist($entity);
            $this->entityManager->flush($entity);
            $this->flash('success', $id === 'new' ? t('The new boat has been added.') : t('The boat has been updated.'));

            return $this->redirect('/dashboard/boats');
        }
    }

    /**
     * Method called via ajax requests to delete the currently editing entity.
     *
     * @param null|mixed $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id = null)
    {
        $errors = $this->app->make('error');
        /* @var \Concrete\Core\Error\ErrorList\ErrorList $errors */
        $rf = $this->app->make(ResponseFactoryInterface::class);
        /* @var ResponseFactoryInterface $rf */
        if (!$this->token->validate('myboats-boats-details-delete-' . $id)) {
            $errors->add($this->token->getErrorMessage());
        } else {
            $entity = $this->idToEntity($id);
            if ($entity === null || $entity->getId() === null) {
                $errors->add(t('Unable to find the specified boat.'));
            } else {
                $this->flash('success', t('The boat has been deleted.'));
                $this->entityManager->remove($entity);
                $this->entityManager->flush($entity);

                return $rf->json(true);
            }
        }

        return $rf->json($errors->jsonSerialize());
    }

    /**
     * Get the entity gived its id (or 'new').
     *
     * @param int|string|mixed $id
     * @param bool $redirectIfNotFound
     *
     * @return Boat|null
     */
    private function idToEntity($id)
    {
        $result = null;
        if ($id === 'new') {
            $result = Boat::create('');
        } else {
            $valn = $this->app->make('helper/validation/numbers');
            /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
            if ($valn->integer($id, 1)) {
                $result = $this->entityManager->find(Boat::class, $id);
            }
        }

        return $result;
    }
}
