document.addEventListener("DOMContentLoaded", () => {
  const addCategoryButton = document.getElementById("btnAddCategory");
  const addCategoryModal = document.getElementById("addCategoryModal");
  const addCategoryForm = document.getElementById("addCategoryForm");
  const closeModalBtns = addCategoryModal.querySelectorAll(
    ".close-btn, .cancel-btn"
  );
  const confirmAddCategory = document.getElementById("confirmAddCategory");
  const categoryTableBody = document.querySelector("#categoryTable tbody");

  // Show modal without hiding the sections
  addCategoryButton.addEventListener("click", () => {
    addCategoryModal.style.display = "block";
    addCategoryForm.reset(); // Reset the form each time the modal is opened
  });

  // Close modal
  closeModalBtns.forEach((btn) =>
    btn.addEventListener("click", () => {
      addCategoryModal.style.display = "none";
    })
  );

  // Handle form submission via AJAX
  confirmAddCategory.addEventListener("click", () => {
    const categoryName = document
      .getElementById("newCategoryName")
      .value.trim();
    const typeName = document.getElementById("newType").value;

    if (!categoryName || !typeName) {
      alert("Please fill in all fields.");
      return;
    }

    // Send AJAX request to add-category.php
    fetch("add-category.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        category_name: categoryName,
        type_name: typeName,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message || "Category added successfully!");

          // Dynamically update the categories table
          const newRow = document.createElement("tr");
          newRow.innerHTML = `
            <td>${data.category_id}</td>
            <td>${categoryName}</td>
            <td>${typeName}</td>
            <td>
              <button class="edit-btn" data-id="${data.category_id}" data-name="${categoryName}" data-type="${typeName}">Uredi</button>
            </td>
          `;
          categoryTableBody.appendChild(newRow);

          // Close the modal
          addCategoryModal.style.display = "none";
        } else {
          alert(data.message || "Failed to add category.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      });
  });
});
