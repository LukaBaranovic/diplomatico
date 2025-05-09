document.addEventListener("DOMContentLoaded", () => {
  // Toggle between categories and items
  const btnCategories = document.getElementById("btnCategories");
  const btnItems = document.getElementById("btnItems");
  const categoriesSection = document.getElementById("categoriesSection");
  const itemsSection = document.getElementById("itemsSection");

  btnCategories.addEventListener("click", () => {
    // Show categories, hide items
    categoriesSection.style.display = "block";
    itemsSection.style.display = "none";

    // Update button styles
    btnCategories.classList.add("active");
    btnItems.classList.remove("active");
  });

  btnItems.addEventListener("click", () => {
    // Show items, hide categories
    categoriesSection.style.display = "none";
    itemsSection.style.display = "block";

    // Update button styles
    btnItems.classList.add("active");
    btnCategories.classList.remove("active");
  });

  // Add event listeners for Izbriši (Delete) buttons in items
  const itemDeleteButtons = document.querySelectorAll("#itemTable .delete-btn");
  itemDeleteButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const itemId = row.querySelector("td:first-child").textContent;
      if (confirm(`Are you sure you want to delete Item ID: ${itemId}?`)) {
        alert(
          `Delete feature for Item ID: ${itemId} will be implemented here.`
        );
        // TODO: Add AJAX call to delete the item
      }
    });
  });

  // Search functionality for categories
  document
    .getElementById("searchBarCategories")
    .addEventListener("keyup", () => {
      filterTable("categoryTable", "searchBarCategories");
    });

  // Search functionality for items
  document.getElementById("searchBarItems").addEventListener("keyup", () => {
    filterTable("itemTable", "searchBarItems");
  });
});

/**
 * Filters the table rows based on the search input.
 * @param {string} tableId - The ID of the table to filter.
 * @param {string} searchBarId - The ID of the search bar.
 */
function filterTable(tableId, searchBarId) {
  const searchValue = document.getElementById(searchBarId).value.toLowerCase();
  const table = document.getElementById(tableId);
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    // Start from 1 to skip the header row
    const cells = rows[i].getElementsByTagName("td");
    let match = false;

    for (let j = 0; j < cells.length - 1; j++) {
      // Skip the last column (buttons)
      if (cells[j].textContent.toLowerCase().includes(searchValue)) {
        match = true;
        break;
      }
    }

    rows[i].style.display = match ? "" : "none";
  }
}
