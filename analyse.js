$(document).ready(function () {
  function fetchItems(startDate, endDate, sortOrder = "DESC") {
    $.ajax({
      url: "fetch_items.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate, sort_order: sortOrder },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("Greška pri dohvaćanju artikala!");
      },
    });
  }

  function fetchCategories(startDate, endDate, sortOrder = "DESC") {
    $.ajax({
      url: "fetch_categories.php",
      method: "POST",
      data: { start_date: startDate, end_date: endDate, sort_order: sortOrder },
      success: function (response) {
        $("#results").html(response);
      },
      error: function () {
        alert("Greška pri dohvaćanju kategorija!");
      },
    });
  }

  function updateCategorySortOrder() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#category-sort-order").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return;
    }

    fetchCategories(startDate, endDate, sortOrder);
  }

  function updateItemSortOrder() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#sort-order").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return;
    }

    fetchItems(startDate, endDate, sortOrder);
  }

  function validateDateFields() {
    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();

    if (new Date(startDate) > new Date(endDate)) {
      alert("Početni datum ne može biti kasnije od završnog!");
      return false;
    }
    return true;
  }

  $("#start-date, #end-date").on("change", function () {
    $(this).blur();
    validateDateFields();
  });

  const defaultStartDate = $("#start-date").val();
  const defaultEndDate = $("#end-date").val();
  const defaultItemSortOrder = $("#sort-order").val();
  fetchItems(defaultStartDate, defaultEndDate, defaultItemSortOrder);

  $("#fetch-items").on("click", function () {
    if (!validateDateFields()) return;

    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#sort-order").val();

    fetchItems(startDate, endDate, sortOrder);
  });

  $(document).on("change", "#sort-order", updateItemSortOrder);

  $("#fetch-categories").on("click", function () {
    if (!validateDateFields()) return;

    const startDate = $("#start-date").val();
    const endDate = $("#end-date").val();
    const sortOrder = $("#category-sort-order").val();

    fetchCategories(startDate, endDate, sortOrder);
  });

  $(document).on("change", "#category-sort-order", updateCategorySortOrder);
});
