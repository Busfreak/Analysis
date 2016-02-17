<section id="main">
<div class="project-header">
<div class="filter-box">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', $filters) ?>
        <?= $this->form->hidden('action', $filters) ?>
        <?= $this->form->hidden('project_id', $filters) ?>
        <?= $this->form->hidden('plugin', $filters) ?>
        <?= $this->form->text('search', $filters, array(), array('placeholder="'.t('Filter').'"'), 'form-input-large') ?>

        <div class="dropdown filters">
            <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Status') ?></a>
            <ul>
                <li><a href="#" class="filter-helper" data-filter=""><?= t('Reset filters') ?></a></li>
                <li><a href="#" class="filter-helper" data-filter="status:open"><?= t('Open tasks') ?></a></li>
                <li><a href="#" class="filter-helper" data-filter="status:closed"><?= t('Closed tasks') ?></a></li>
            </ul>
        </div>

        <?php if (isset($categories) && ! empty($categories)): ?>
        <div class="dropdown filters">
            <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Categories') ?></a>
            <ul>
                <li><a href="#" class="filter-helper" data-append-filter="category:none"><?= t('No category') ?></a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="#" class="filter-helper" data-append-filter='category:"<?= $this->e($category) ?>"'><?= $this->e($category) ?></a></li>
                <?php endforeach ?>
            </ul>
        </div>
        <?php endif ?>
 
        <?php if (isset($swimlanes) && ! empty($swimlanes)): ?>
        <div class="dropdown filters">
            <i class="fa fa-caret-down"></i> <a href="#" class="dropdown-menu"><?= t('Swimlanes') ?></a>
            <ul>
                <li><a href="#" class="filter-helper" data-append-filter="status:open"><?= t('All swimlanes') ?></a></li>
                <?php foreach ($swimlanes as $swimlane): ?>
                    <li><a href="#" class="filter-helper" data-append-filter='swimlane:"<?= $this->e($swimlane['name']) ?>"'><?= $this->e($swimlane['name']) ?></a></li>
                <?php endforeach ?>
            </ul>
        </div>
        <?php endif ?>

    </form>
    </div>
</div>
</section>
