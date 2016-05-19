<div class="comment <?= isset($preview) ? 'comment-preview' : '' ?>" id="comment-<?= $comment['id'] ?>">

    <?= $this->avatar->render($comment['user_id'], $comment['username'], $comment['name'], $comment['email'], $comment['avatar_path']) ?>

    <div class="comment-title">
        <?php if (! empty($comment['username'])): ?>
            <span class="comment-username"><?= $this->text->e($comment['name'] ?: $comment['username']) ?></span>
        <?php endif ?>

        <span class="comment-date"><?= $this->dt->datetime($comment['date_creation']) ?></span>
    </div>

    <div class="comment-content">
        <div class="markdown">
            <?= $this->text->markdown($comment['comment'], isset($is_public) && $is_public) ?>
        </div>
    </div>

</div>
