$(document).ready(function () {
  // Function to fetch items
  function fetchItems(startDate, endDate) {
    $.ajax({
      url: "fetch_items.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("An error occurred while fetching items.");
      },
    });
  }

  // Function to fetch categories
  function fetchCategories(startDate, endDate) {
    $.ajax({
      url: "fetch_categories.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("An error occurred while fetching categories.");
      },
    });
  }

  // Default behavior: Fetch items on page load
  const defaultStartDate = $("#start-date").val();
  const defaultEndDate = $("#end-date").val();
  fetchItems(defaultStartDate, defaultEndDate);

  // Fetch items when "Fetch Items" button is clicked
  $("#fetch-items").on("click", function () {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Start date cannot be later than end date!");
      return;
    }

    fetchItems(startDate, endDate);
  });

  // Fetch categories when "Fetch Categories" button is clicked
  $("#fetch-categories").on("click", function () {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Start date cannot be later than end date!");
      return;
    }

    fetchCategories(startDate, endDate);
  });
});
