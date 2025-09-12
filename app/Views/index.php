<?= $this->extend('template') ?>

<?= $this->section('title') ?>Home<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="text-dark">Hello</h1>
<p class="text-dark fs-5">
  Welcome to our simple CodeIgniter website. You can explore more by visiting the 
  <strong>About</strong> or <strong>Contact</strong> pages.
</p>
<ul class="text-dark fs-5">
    <li> Built with CodeIgniter 4</li>
    <li> Simple Bootstrap styling</li>
</ul>
<?= $this->endSection() ?>
