document.addEventListener("DOMContentLoaded", () => {
  const addCategoryButton = document.getElementById("btnAddCategory");
  const addCategoryModal = document.getElementById("addCategoryModal");
  const addCategoryForm = document.getElementById("addCategoryForm");
  const closeBtn = addCategoryModal.querySelector(".close-btn");
  const cancelButton = addCategoryModal.querySelector(".cancel-btn");
  const confirmAddCategory = document.getElementById("confirmAddCategory");
  const categoriesSection = document.getElementById("categoriesSection");
  const itemsSection = document.getElementById("itemsSection");
  const errorMessage = document.getElementById("addCategoryErrorMessage");
  const successMessage = document.getElementById("addCategorySuccessMessage");
  const categoryTableBody = document.querySelector("#categoryTable tbody");

  // Show modal and hide other sections
  addCategoryButton.addEventListener("click", () => {
    addCategoryModal.style.display = "block";
    categoriesSection.style.display = "none";
    itemsSection.style.display = "none";
    resetMessages(); // Reset messages and form fields
  });

  // Close modal and show other sections (Close button)
  closeBtn.addEventListener("click", closeModal);

  // Close modal and show other sections (Cancel button)
  cancelButton.addEventListener("click", closeModal);

  // Handle form submission
  confirmAddCategory.addEventListener("click", () => {
    const categoryName = document
      .getElementById("newCategoryName")
      .value.trim();
    const typeName = document.getElementById("newType").value;

    if (!categoryName || !typeName) {
      displayMessage(errorMessage, "Please fill in all fields.", "error");
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
          // Show success message with animation
          displayMessage(
            successMessage,
            data.message || "Category added successfully!",
            "success"
          );

          // Dynamically add the new category to the table
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

          // Reset the form for adding another category
          addCategoryForm.reset();
        } else {
          displayMessage(
            errorMessage,
            data.message || "Failed to add category.",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        displayMessage(
          errorMessage,
          "An error occurred. Please try again.",
          "error"
        );
      });
  });

  /**
   * Function to close the modal and reset sections
   */
  function closeModal() {
    addCategoryModal.style.display = "none";
    categoriesSection.style.display = "block";
    itemsSection.style.display = "block";
    resetMessages();
  }

  /**
   * Function to display a message with animation (success or error)
   * @param {HTMLElement} element - The DOM element where the message will be displayed
   * @param {string} message - The message to display
   * @param {string} type - The type of message ('success' or 'error')
   */
  function displayMessage(element, message, type) {
    element.textContent = message;
    element.style.fontSize = "1.5rem"; // Larger size
    element.style.display = "block";
    element.style.color = type === "success" ? "green" : "red"; // Set color based on type

    // Shrink the message after 1 second
    setTimeout(() => {
      element.style.fontSize = "1rem"; // Regular size
    }, 1000);
  }

  /**
   * Function to reset all messages
   */
  function resetMessages() {
    errorMessage.style.display = "none";
    successMessage.style.display = "none";
    errorMessage.textContent = "";
    successMessage.textContent = "";
  }
});
