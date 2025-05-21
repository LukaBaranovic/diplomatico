document.addEventListener("DOMContentLoaded", () => {
  const deleteButton = document.getElementById("deleteCategoryBtn");

  deleteButton.addEventListener("click", () => {
    const categoryId = document.getElementById("categoryId").value;

    if (confirm("Jeste li sigurni da želite obrisati ovu kategoriju?")) {
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
            document
              .querySelector(
                `#categoryTable .edit-btn[data-id="${categoryId}"]`
              )
              .closest("tr")
              .remove();

            document.getElementById("editCategoryModal").style.display = "none";
          } else {
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
