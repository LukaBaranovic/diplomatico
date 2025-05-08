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

  // Add event listeners for Uredi (Edit) buttons in categories
  const categoryEditButtons = document.querySelectorAll(
    "#categoryTable .edit-btn"
  );
  categoryEditButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const categoryId = row.querySelector("td:first-child").textContent.trim();
      const categoryName = row
        .querySelector("td:nth-child(2)")
        .textContent.trim();
      const typeName = row.querySelector("td:nth-child(3)").textContent.trim();

      // Populate the modal fields
      document.getElementById("categoryId").value = categoryId;
      document.getElementById("categoryName").value = categoryName;

      const typeDropdown = document.getElementById("typeName");
      const options = typeDropdown.options;

      // Set the correct type as selected in the dropdown
      for (let i = 0; i < options.length; i++) {
        if (options[i].value === typeName) {
          options[i].selected = true;
          break;
        }
      }

      // Show the modal
      document.getElementById("editCategoryModal").style.display = "block";
    });
  });

  // Add event listeners for Izbriši (Delete) buttons in categories
  const categoryDeleteButtons = document.querySelectorAll(
    "#categoryTable .delete-btn"
  );
  categoryDeleteButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const categoryId = row.querySelector("td:first-child").textContent;
      if (
        confirm(`Are you sure you want to delete Category ID: ${categoryId}?`)
      ) {
        alert(
          `Delete feature for Category ID: ${categoryId} will be implemented here.`
        );
        // TODO: Add AJAX call to delete the category
      }
    });
  });

  // Add event listeners for Uredi (Edit) buttons in items
  const itemEditButtons = document.querySelectorAll("#itemTable .edit-btn");
  itemEditButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const itemId = row.querySelector("td:first-child").textContent;
      alert(`Edit feature for Item ID: ${itemId} will be implemented here.`);
      // TODO: Add AJAX or navigation to edit page for item
    });
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

  // Close the modal
  document.querySelector(".cancel-btn").addEventListener("click", () => {
    document.getElementById("editCategoryModal").style.display = "none";
  });

  document.querySelector(".close-btn").addEventListener("click", () => {
    document.getElementById("editCategoryModal").style.display = "none";
  });

  // Confirm button logic for editing category
  document.getElementById("confirmEdit").addEventListener("click", () => {
    const formData = new FormData(document.getElementById("editCategoryForm"));

    // AJAX request to update the category
    fetch("edit-category.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload(); // Reload the page to see the changes
        } else {
          document.getElementById("errorMessage").textContent = data.message;
          document.getElementById("errorMessage").style.display = "block";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
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
