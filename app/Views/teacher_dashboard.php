<?php $this->extend('template/header'); ?>

<?php $this->section('content'); ?>

<h1><?= esc($message) ?></h1>  <!-- This will display "Welcome, Teacher!" or whatever $message is -->

<?php $this->endSection(); ?>