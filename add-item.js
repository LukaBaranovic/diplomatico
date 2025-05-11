document.addEventListener("DOMContentLoaded", function () {
  const addItemBtn = document.getElementById("btnAddItem");
  const addItemModal = document.getElementById("addItemModal");
  const closeModalBtns = addItemModal.querySelectorAll(
    ".close-btn, .cancel-btn"
  );
  const addItemForm = document.getElementById("addItemForm");

  // Show modal
  addItemBtn.addEventListener("click", function () {
    addItemModal.style.display = "block";
  });

  // Close modal
  closeModalBtns.forEach((btn) =>
    btn.addEventListener("click", function () {
      addItemModal.style.display = "none";
    })
  );

  // Perform client-side validation
  function validateForm(formData) {
    const itemName = formData.get("item_name");
    const itemPrice = formData.get("item_price");
    const categoryId = formData.get("category_id");

    // Check if all fields are filled
    if (!itemName || !itemPrice || !categoryId) {
      alert("All fields are required.");
      return false;
    }

    // Validate that price is a positive number
    const price = parseFloat(itemPrice);
    if (isNaN(price) || price <= 0) {
      alert("Item price must be a valid positive number.");
      return false;
    }

    return true;
  }

  // Submit form via AJAX
  addItemForm.addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(addItemForm);

    // Perform client-side validation
    if (!validateForm(formData)) {
      return; // Stop submission if validation fails
    }

    fetch("add-item.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Show success alert
          alert(data.message);

          // Add the new item to the Items table
          const itemTable = document
            .getElementById("itemTable")
            .getElementsByTagName("tbody")[0];
          const newRow = itemTable.insertRow();
          newRow.innerHTML = `
                      <td>${data.item_id}</td>
                      <td>${data.item_name}</td>
                      <td>${data.item_price}</td>
                      <td>${data.category_name}</td>
                      <td>
                          <button class="edit-btn" data-id="${data.item_id}" data-name="${data.item_name}" data-price="${data.item_price}" data-category-id="${data.category_id}">Uredi</button>
                      </td>
                  `;

          // Reset the form and close the modal
          addItemForm.reset();
          addItemModal.style.display = "none";
        } else {
          // Show error alert
          alert(data.message);
        }
      })
      .catch((error) => {
        // Show generic error alert
        alert("An error occurred. Please try again.");
      });
  });
});
