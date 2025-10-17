<?php $this->extend('template/header'); ?>

<?php $this->section('content'); ?>
<h1>Latest Announcements</h1>

<?php if (empty($announcements)): ?>
    <p>No announcements available.</p>
<?php else: ?>
    <?php foreach ($announcements as $announcement): ?>
        <div class="announcement">
            <h2><?= esc($announcement['title']) ?></h2>
            <p><?= esc($announcement['content']) ?></p>
            <p class="posted-on">Posted on: <?= esc($announcement['created_at']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php $this->endSection(); ?>
