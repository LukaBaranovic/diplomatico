document.addEventListener("DOMContentLoaded", () => {
  const addCategoryButton = document.getElementById("btnAddCategory");
  const addCategoryModal = document.getElementById("addCategoryModal");
  const addCategoryForm = document.getElementById("addCategoryForm");
  const closeBtn = addCategoryModal.querySelector(".close-btn");
  const cancelButton = addCategoryModal.querySelector(".cancel-btn");
  const confirmAddCategory = document.getElementById("confirmAddCategory");
  const categoriesSection = document.getElementById("categoriesSection");
  const itemsSection = document.getElementById("itemsSection");
  const categoryTableBody = document.querySelector("#categoryTable tbody");

  // Variable to track the currently visible section
  let currentSection = "categories"; // Default to categories section

  // Show modal and hide other sections
  addCategoryButton.addEventListener("click", () => {
    // Track which section is currently visible
    if (categoriesSection.style.display === "block") {
      currentSection = "categories";
    } else if (itemsSection.style.display === "block") {
      currentSection = "items";
    }

    // Show the modal and hide both sections
    addCategoryModal.style.display = "block";
    categoriesSection.style.display = "none";
    itemsSection.style.display = "none";
    addCategoryForm.reset();
  });

  // Close modal (Close button)
  closeBtn.addEventListener("click", closeModal);

  // Close modal (Cancel button)
  cancelButton.addEventListener("click", closeModal);

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

          // Dynamically update the categories table if on the categories section
          if (currentSection === "categories") {
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
          }

          // Close the modal and show the previously active section
          closeModal();
        } else {
          alert(data.message || "Failed to add category.");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      });
  });

  /**
   * Function to close the modal and reset sections
   */
  function closeModal() {
    addCategoryModal.style.display = "none";

    // Show only the previously active section
    if (currentSection === "categories") {
      categoriesSection.style.display = "block";
      itemsSection.style.display = "none";
    } else if (currentSection === "items") {
      itemsSection.style.display = "block";
      categoriesSection.style.display = "none";
    }
  }
});
