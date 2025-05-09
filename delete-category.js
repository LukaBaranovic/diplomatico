document.addEventListener("DOMContentLoaded", () => {
  // Delete button inside the edit modal
  const deleteButton = document.getElementById("deleteCategoryBtn");

  deleteButton.addEventListener("click", () => {
    const categoryId = document.getElementById("categoryId").value;

    if (confirm("Are you sure you want to delete this category?")) {
      // Send AJAX request to delete the category
      fetch("delete-category.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ category_id: categoryId }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // Remove the category row from the table
            document
              .querySelector(
                `#categoryTable .edit-btn[data-id="${categoryId}"]`
              )
              .closest("tr")
              .remove();

            // Close the modal
            document.getElementById("editCategoryModal").style.display = "none";
          } else {
            // Show the error message
            const errorMessage = document.getElementById("errorMessage");
            errorMessage.textContent = data.message;
            errorMessage.style.display = "block";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    }
  });
});
