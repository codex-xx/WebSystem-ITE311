<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-4">
  <div class="row mb-3">
    <div class="col-md-8">
      <input id="searchBox" class="form-control" placeholder="Type to filter courses (client-side)..." />
    </div>
    <div class="col-md-4">
      <form id="serverSearchForm" class="d-flex">
        <input name="term" id="serverSearchInput" class="form-control me-2" placeholder="Search server-side..." />
        <button class="btn btn-primary" type="submit">Search</button>
      </form>
    </div>
  </div>

  <div id="searchStatus" class="mb-2"></div>

  <div id="coursesList" class="list-group">
    <?php if (!empty($courses)): ?>
      <?php foreach ($courses as $c): ?>
        <div class="list-group-item course-item" data-title="<?= esc(strtolower($c['title'])) ?>" data-desc="<?= esc(strtolower($c['description'])) ?>">
          <h5><?= esc($c['title']) ?></h5>
          <p><?= esc($c['description']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info">No courses available.</div>
    <?php endif; ?>
  </div>
</div>


<!-- SCRIPTS -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
  // Client-side: instant filtering
  $('#searchBox').on('input', function(){
    const term = $(this).val().trim().toLowerCase();
    $('#coursesList .course-item').each(function(){
      const title = $(this).data('title') || '';
      const desc  = $(this).data('desc')  || '';
      const visible = term === '' || title.indexOf(term) !== -1 || desc.indexOf(term) !== -1;
      $(this).toggle(visible);
    });
  });

  // Server-side: AJAX search
  $('#serverSearchForm').on('submit', function(e){
    e.preventDefault();
    const term = $('#serverSearchInput').val().trim();
    $('#searchStatus').text('Searching...');
    $.ajax({
      url: '<?= site_url("courses/search") ?>',
      method: 'GET',
      data: { term: term },
      dataType: 'json'
    }).done(function(data){
      $('#coursesList').empty();
      if (!data || data.length === 0) {
        $('#coursesList').html('<div class="alert alert-warning">No courses match "' + $('<div>').text(term).html() + '".</div>');
      } else {
        data.forEach(function(c){
          const item = '<div class="list-group-item course-item" data-title="'+(c.title||'')+'" data-desc="'+(c.description||'')+'">'
                     + '<h5>'+ $('<div>').text(c.title).html() +'</h5>'
                     + '<p>'+ $('<div>').text(c.description).html() +'</p>'
                     + '</div>';
          $('#coursesList').append(item);
        });
      }
      $('#searchStatus').text('Showing ' + data.length + ' result(s).');
    }).fail(function(xhr, status, err){
      $('#searchStatus').text('Search failed: ' + status);
    });
  });
});
</script>

</body>
</html>
