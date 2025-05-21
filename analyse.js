$(document).ready(function () {
  // Function to fetch items
  function fetchItems(startDate, endDate, sortOrder = "DESC") {
    $.ajax({
      url: "fetch_items.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate, sort_order: sortOrder },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("Greška pri dohvaćanju artikala.");
      },
    });
  }

  // Function to fetch categories
  function fetchCategories(startDate, endDate, sortOrder = "DESC") {
    $.ajax({
      url: "fetch_categories.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate, sort_order: sortOrder },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("Greška pri dohvaćanju kategorija.");
      },
    });
  }

  // Function to update sort order for categories
  function updateCategorySortOrder() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#category-sort-order").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return;
    }

    // Fetch categories with the updated sort order
    fetchCategories(startDate, endDate, sortOrder);
  }

  // Function to update sort order for items
  function updateItemSortOrder() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#sort-order").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return;
    }

    // Fetch items with the updated sort order
    fetchItems(startDate, endDate, sortOrder);
  }

  // Ensure date fields retain their values
  function validateDateFields() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();

    // Ensure valid date range
    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return false;
    }
    return true;
  }

  // --- DATE PICKER AUTO-CLOSE ---
  // This will close the date picker after a date is selected
  $("#start-date, #end-date").on("change", function () {
    $(this).blur();
    validateDateFields(); // Validate dates whenever they change
  });

  // Default behavior: Fetch items on page load
  const defaultStartDate = $("#start-date").val();
  const defaultEndDate = $("#end-date").val();
  const defaultItemSortOrder = $("#sort-order").val(); // Default sort order for items
  fetchItems(defaultStartDate, defaultEndDate, defaultItemSortOrder);

  // Fetch items when "Fetch Items" button is clicked
  $("#fetch-items").on("click", function () {
    if (!validateDateFields()) return;

    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#sort-order").val();

    fetchItems(startDate, endDate, sortOrder);
  });

  // Attach onchange event listener for item sort order dropdown
  $(document).on("change", "#sort-order", updateItemSortOrder);

  // Fetch categories when "Fetch Categories" button is clicked
  $("#fetch-categories").on("click", function () {
    if (!validateDateFields()) return;

    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#category-sort-order").val();

    fetchCategories(startDate, endDate, sortOrder);
  });

  // Attach onchange event listener for category sort order dropdown
  $(document).on("change", "#category-sort-order", updateCategorySortOrder);
});
