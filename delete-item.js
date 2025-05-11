document.addEventListener("DOMContentLoaded", () => {
  const deleteButton = document.getElementById("deleteItemBtn");

  deleteButton.addEventListener("click", () => {
    const itemId = document.getElementById("itemId").value;

    if (confirm("Are you sure you want to delete this item?")) {
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
            document
              .querySelector(`#itemTable .edit-btn[data-id="${itemId}"]`)
              .closest("tr")
              .remove();

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
