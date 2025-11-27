<?= $this->extend('template') ?>

<?= $this->section('title') ?>Courses<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row mb-3">
  <div class="col-md-10">
    <input id="unifiedSearchBox" class="form-control" placeholder="Search courses..." />
  </div>
  <div class="col-md-2">
    <button id="searchButton" class="btn btn-primary w-100" type="button">Search</button>
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

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
  let searchTimeout;

  // Function to perform server-side search
  function performServerSearch(term) {
    $('#searchStatus').text('Searching...');
    $.ajax({
      url: '<?= site_url("course/search") ?>',
      method: 'POST',
      data: { term: term },
      dataType: 'json'
    }).done(function(data){
      $('#coursesList').empty();
      if (!data || data.length === 0) {
        $('#coursesList').html('<div class="alert alert-warning">No courses match "' + $('<div>').text(term).html() + '".</div>');
      } else {
        data.forEach(function(c){
          const item = '<div class="list-group-item course-item" data-title="'+(c.title ? c.title.toLowerCase() : '')+'" data-desc="'+(c.description ? c.description.toLowerCase() : '')+'">'
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
  }

  // Client-side filtering function
  function performClientFilter(term) {
    const lowerTerm = term.toLowerCase();
    $('#coursesList .course-item').each(function(){
      const title = $(this).data('title') || '';
      const desc  = $(this).data('desc')  || '';
      const visible = term === '' || title.indexOf(lowerTerm) !== -1 || desc.indexOf(lowerTerm) !== -1;
      $(this).toggle(visible);
    });
  }

  // Unified search with both client-side and server-side functionality
  $('#unifiedSearchBox').on('input', function(){
    const term = $(this).val().trim();

    // Clear previous timeout
    clearTimeout(searchTimeout);

    // Client-side: instant filtering of current results
    performClientFilter(term);

    // Server-side: AJAX search with debounce
    if (term.length >= 2) {  // Only search server-side if 2+ characters
      searchTimeout = setTimeout(function(){
        performServerSearch(term);
      }, 300); // 300ms debounce
    } else if (term.length === 0) {
      // Clear search for empty term
      $('#searchStatus').text('');
    }
  });

  // Search button click handler
  $('#searchButton').on('click', function(){
    const term = $('#unifiedSearchBox').val().trim();
    // Clear any pending timeout
    clearTimeout(searchTimeout);

    if (term.length >= 1) {  // Allow search even with 1 character when button is clicked
      performServerSearch(term);
    } else {
      // Reset to show all courses if empty search
      $('#coursesList .course-item').show();
      $('#searchStatus').text('');
    }
  });
});
</script>

<?= $this->endSection() ?>
