document.addEventListener("DOMContentLoaded", () => {
  // Event delegation for Izbriši (Delete) buttons
  document
    .getElementById("categoryTable")
    .addEventListener("click", (event) => {
      if (event.target.classList.contains("delete-btn")) {
        const button = event.target;
        const categoryId = button.dataset.id;

        // Show the delete modal
        const deleteModal = document.getElementById("deleteCategoryModal");
        deleteModal.style.display = "block";

        // Attach category ID to confirm button
        const confirmButton = document.getElementById("confirmDeleteCategory");
        confirmButton.dataset.id = categoryId;

        // Close the modal on cancel
        document
          .querySelectorAll(".cancel-btn, .close-btn")
          .forEach((cancelButton) => {
            cancelButton.addEventListener("click", () => {
              deleteModal.style.display = "none";
              document.getElementById("deleteErrorMessage").style.display =
                "none";
            });
          });
      }
    });

  // Confirm delete logic
  document
    .getElementById("confirmDeleteCategory")
    .addEventListener("click", (event) => {
      const categoryId = event.target.dataset.id;

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
                `#categoryTable .delete-btn[data-id="${categoryId}"]`
              )
              .closest("tr")
              .remove();

            // Close the modal
            document.getElementById("deleteCategoryModal").style.display =
              "none";
          } else {
            // Show the error message
            const errorMessage = document.getElementById("deleteErrorMessage");
            errorMessage.textContent = data.message;
            errorMessage.style.display = "block";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    });
});
