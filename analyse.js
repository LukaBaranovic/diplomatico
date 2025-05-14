$(document).ready(function () {
  function fetchItems(startDate, endDate) {
    $.ajax({
      url: "fetch_items.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("An error occurred while fetching data.");
      },
    });
  }

  // Fetch items on button click
  $("#fetch-data").on("click", function () {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Start date cannot be later than end date!");
      return;
    }

    fetchItems(startDate, endDate);
  });

  // Fetch items for the default date range on load
  const defaultStartDate = $("#start-date").val();
  const defaultEndDate = $("#end-date").val();
  fetchItems(defaultStartDate, defaultEndDate);
});
