<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Core\Application\Service\UserInterface $interface */
/* @var Concrete\Core\Application\Service\Dashboard $dashboard */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Theme\Dashboard\PageTheme $theme */

/* @var Concrete\Package\MyBoats\Controller\SinglePage\Dashboard\Boats $controller */

/* @var string $name */
/* @var enabled $enabled */
/* @var int $paginationSize */
/* @var array $allowedPaginationSizes */
/* @var MyBoats\Search\Results\Boats $result */

?>
<div class="ccm-dashboard-header-buttons">
    <a href="<?= $view->action('details', 'new') ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i>
        <?= t('Add boat') ?>
    </a>
</div>
<form class="ccm-search-fields" role="form" action="<?= $view->action('') ?>" method="POST">
    <?php $token->output('myboats-boats-search') ?>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <?= $form->label('name', t('Name')) ?>
                <div class="ccm-search-field-content">
                    <?= $form->text('name', $name) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?= $form->label('enabled', t('Enabled')) ?>
                <div class="ccm-search-field-content">
                    <?= $form->select('enabled', ['' => '', 'yes' => t('Yes'), 'no' => t('No')]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?= $form->label('paginationSize', t('Items per page')) ?>
                <div class="ccm-search-field-content">
                    <?= $form->select('paginationSize', $allowedPaginationSizes, $paginationSize) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="ccm-search-fields-submit">
        <button type="submit" class="btn btn-primary pull-right"><?= t('Search') ?></button>
    </div>
</form>

<?php
$items = $result->getItems();
/* @var MyBoats\Search\Results\Items\Boats[] $items */
if (empty($items)) {
    ?>
    <div class="alert alert-warning">
        <?= t('No boats found.') ?>
    </div>
    <?php
} else {
    ?>
    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <?php
                        foreach ($result->getColumns() as $column) {
                            if ($column->isColumnSortable()) {
                                ?><th style="padding-left: 15px" class="<?= $column->getColumnStyleClass() ?>"><a href="<?= $column->getColumnSortURL() ?>"><?= h($column->getColumnTitle()) ?></a></th><?php
                            } else {
                                ?><th><span><?= h($column->getColumnTitle()) ?></span></th><?php
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($items as $item) {
                        /* @var MyBoats\Search\Results\Item\Boats $item */
                        ?>
                        <tr class="boat <?= $item->getRowClass() ?>" data-boat-details-url="<?= h($item->getViewUrl()) ?>">
                            <?php
                            foreach ($item->getColumns() as $column) {
                                ?>
                                <td style="padding-left: 15px"><?= nl2br(h($column->getColumnValue())) ?></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        $pagination = $result->getPaginationHTML();
        if ($pagination) {
            ?>
            <div class="ccm-search-results-pagination">
                <?= $pagination ?>
            </div>
            <?php
        }
        ?>
    </div>
    <script>
    (function() {
        function setup() {
            var r = false;
            try {
                var $rows = $('tr[data-boat-details-url]');
                if ($rows.length > 0) {
                    $rows.on('click', function () {
                        window.location.href = $(this).data('boat-details-url');
                    });
                    r = true;
                }
            } catch (e) {
            }
        }
        if (setup() === false) {
            $(document).ready(function() {
                setup();
            });
        }
    })();
    </script>
    <?php
}
