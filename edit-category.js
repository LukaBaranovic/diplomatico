document.addEventListener("DOMContentLoaded", () => {
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
