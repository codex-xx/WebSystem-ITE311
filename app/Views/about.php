<?= $this->extend('template') ?>

<?= $this->section('title') ?>About<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center">
    <h1 class="text-dark fw-bold mb-3">About Us</h1>
    <p class="text-dark fs-5">
        Welcome to the About page! <br>
        Our project is built with <span class="text-danger fw-semibold">CodeIgniter 4</span>
    </p>
    <h3 class="text-dark">Our Mission</h3>
    <p class="text-dark fs-5">
            Our goal is to build a simple, clean, and responsive web application that’s easy to use and effortless to learn
    </p>
    </div>
</div>
<?= $this->endSection() ?>
