document.addEventListener("DOMContentLoaded", () => {
  const deleteButton = document.getElementById("deleteItemBtn");

  deleteButton.addEventListener("click", () => {
    const itemId = document.getElementById("itemId").value;

    if (confirm("Jeste li sigurni da želite obrisati ovaj artikal?")) {
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
              data.message || "Greška pri brisanju artikla!";
            errorMessage.style.display = "block";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Greška pri brisanju artikla!");
        });
    }
  });
});
