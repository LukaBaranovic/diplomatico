document.addEventListener("DOMContentLoaded", () => {
  const deleteButton = document.getElementById("deleteItemBtn");

  deleteButton.addEventListener("click", () => {
    const itemId = document.getElementById("itemId").value;

    if (confirm("Are you sure you want to delete this item?")) {
      // Send AJAX request to delete the item
      fetch("delete-item.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ item_id: itemId }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // Remove the item row from the table
            document
              .querySelector(`#itemTable .edit-btn[data-id="${itemId}"]`)
              .closest("tr")
              .remove();

            // Close the modal
            document.getElementById("editItemModal").style.display = "none";
          } else {
            const errorMessage = document.getElementById("itemErrorMessage");
            errorMessage.textContent =
              data.message || "Failed to delete the item.";
            errorMessage.style.display = "block";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while deleting the item.");
        });
    }
  });
});
