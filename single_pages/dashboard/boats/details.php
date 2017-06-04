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

/* @var Concrete\Package\MyBoats\Controller\SinglePage\Dashboard\Boats\Details $controller */

/* @var MyBoats\Entity\Boat $boat */

$id = $boat->getId() ?: 'new';
?>

<form method="post" action="<?= $view->action('save', $id) ?>">
    <?php $token->output('myboats-boats-details-' . $id)?>
    <div class="form-group">
        <?= $form->label('name', t('Name')) ?>
        <div class="input-group">
            <?= $form->text('name', $boat->getName(), ['maxlength' => '255', 'required' => 'required']) ?>
            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('enabled', tc('Boat', 'Enabled/active')) ?>
        <div class="checkbox">
            <label><?= $form->radio('enabled', '1', $boat->isEnabled() ? '1' : '0', ['required' => 'required']) ?> <?= tc('Boat', 'Enabled (published)') ?></label>
        </div>
        <div class="checkbox">
            <label><?= $form->radio('enabled', '0', $boat->isEnabled() ? '1' : '0', ['required' => 'required']) ?> <?= tc('Boat', 'Disabled (not published)') ?></label>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('length', t('Length')) ?>
        <?= $form->number('length', $boat->getLength(), ['min' => '1', 'max' => PHP_INT_MAX, 'step' => '1']) ?>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
	       <a href="<?= URL::to('/dashboard/boats') ?>" class="btn btn-default"><?= t('Cancel') ?></a>
            <div class="pull-right">
                <?php
                if ($id !== 'new') {
                    ?>
                    <a class="btn btn-danger" data-launch-dialog="myboats-boats-details-delete-dialog"><?= t('Delete') ?></a>
                    <?php
                }
                ?>
                <input type="submit" class="btn btn-primary" value="<?= $id === 'new' ? t('Create') : t('Save') ?>" />
            </div>
        </div>
    </div>
</form>
<?php
if ($id !== 'new') {
    ?>
    <div style="display: none" data-dialog="myboats-boats-details-delete-dialog" class="ccm-ui">
        <form data-dialog-form="myboats-boats-details-delete-form" method="POST" action="<?= $view->action('delete', $id) ?>">
            <?php $token->output('myboats-boats-details-delete-' . $id) ?>
            <p><?= t('Are you sure you want to permanently delete this boat?') ?></p>
            <p><strong><?= t('WARNING: this operation can not be undone!') ?></strong></p>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button class="btn btn-danger pull-right" data-dialog-action="submit"><?= t('Delete') ?></button>
        </div>
    </div>
    <script>
    $(function() {
        var $dialog = $('div[data-dialog="myboats-boats-details-delete-dialog"]');
        $('[data-launch-dialog="myboats-boats-details-delete-dialog"]').on('click', function(e) {
            e.preventDefault();
            jQuery.fn.dialog.open({
                element: $dialog,
                modal: true,
                width: 420,
                title: <?= json_encode(t('Removal confirmation')) ?>,
                height: 'auto'
            });
        });
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form === 'myboats-boats-details-delete-form') {
                window.location.href = <?= json_encode((string) URL::to('/dashboard/boats')) ?>;
            }
        });
    });
    </script>
    <?php
}
