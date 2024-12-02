# diplomatico



$(document).ready(function (){
  $('.update-button').on('click', function() {
    $tr = $(this).closest('tr');

    var data = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    console.log(data);

    $('#category_id_fetched').val(data[0]);
    $('#category_name_fetched').val(data[1]);


  });
});
