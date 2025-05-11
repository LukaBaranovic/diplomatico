document.addEventListener("DOMContentLoaded", () => {
  const itemEditButtons = document.querySelectorAll("#itemTable .edit-btn");
  itemEditButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const itemId = row.querySelector("td:first-child").textContent.trim();
      const itemName = row.querySelector("td:nth-child(2)").textContent.trim();
      const itemPrice = row.querySelector("td:nth-child(3)").textContent.trim();
      const categoryId =
        row.querySelector("td:nth-child(4)").dataset.categoryId;

      document.getElementById("itemId").value = itemId;
      document.getElementById("itemName").value = itemName;
      document.getElementById("itemPrice").value = itemPrice;
      document.getElementById("itemCategory").value = categoryId;

      document.getElementById("editItemModal").style.display = "block";
    });
  });

  document.querySelectorAll(".cancel-btn, .close-btn").forEach((button) => {
    button.addEventListener("click", () => {
      document.getElementById("editItemModal").style.display = "none";
    });
  });

  document.getElementById("confirmEditItem").addEventListener("click", () => {
    const formData = new FormData(document.getElementById("editItemForm"));

    const itemPrice = parseFloat(formData.get("item_price"));
    if (isNaN(itemPrice) || itemPrice <= 0) {
      document.getElementById("itemErrorMessage").textContent =
        "Price must be a positive number greater than 0";
      document.getElementById("itemErrorMessage").style.display = "block";
      return;
    }

    fetch("edit-item.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);

          const row = document
            .querySelector(
              `#itemTable .edit-btn[data-id="${formData.get("item_id")}"]`
            )
            .closest("tr");
          row.querySelector("td:nth-child(2)").textContent =
            formData.get("item_name");
          row.querySelector("td:nth-child(3)").textContent = parseFloat(
            formData.get("item_price")
          ).toFixed(2);
          row.querySelector("td:nth-child(4)").textContent =
            document.querySelector(
              `#itemCategory option[value="${formData.get("category_id")}"]`
            ).textContent;

          document.getElementById("editItemModal").style.display = "none";
        } else {
          document.getElementById("itemErrorMessage").textContent =
            data.message;
          document.getElementById("itemErrorMessage").style.display = "block";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });
});
