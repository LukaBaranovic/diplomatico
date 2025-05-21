document.addEventListener("DOMContentLoaded", () => {
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

      document.getElementById("categoryId").value = categoryId;
      document.getElementById("categoryName").value = categoryName;

      const typeDropdown = document.getElementById("typeName");
      const options = typeDropdown.options;

      for (let i = 0; i < options.length; i++) {
        if (options[i].value === typeName) {
          options[i].selected = true;
          break;
        }
      }

      // CLEAR ERROR/SUCCESS MESSAGES every time modal is opened
      document.getElementById("errorMessage").textContent = "";
      document.getElementById("errorMessage").style.display = "none";
      document.getElementById("successMessage").textContent = "";
      document.getElementById("successMessage").style.display = "none";

      document.getElementById("editCategoryModal").style.display = "block";
    });
  });

  document.querySelector(".cancel-btn").addEventListener("click", () => {
    document.getElementById("editCategoryModal").style.display = "none";
    // Optionally clear messages on close, too:
    document.getElementById("errorMessage").textContent = "";
    document.getElementById("errorMessage").style.display = "none";
    document.getElementById("successMessage").textContent = "";
    document.getElementById("successMessage").style.display = "none";
  });

  document.querySelector(".close-btn").addEventListener("click", () => {
    document.getElementById("editCategoryModal").style.display = "none";
    // Optionally clear messages on close, too:
    document.getElementById("errorMessage").textContent = "";
    document.getElementById("errorMessage").style.display = "none";
    document.getElementById("successMessage").textContent = "";
    document.getElementById("successMessage").style.display = "none";
  });

  document.getElementById("confirmEdit").addEventListener("click", () => {
    const formData = new FormData(document.getElementById("editCategoryForm"));

    fetch("edit-category.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
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
