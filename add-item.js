document.addEventListener("DOMContentLoaded", function () {
  const addItemBtn = document.getElementById("btnAddItem");
  const addItemModal = document.getElementById("addItemModal");
  const closeModalBtns = addItemModal.querySelectorAll(
    ".close-btn, .cancel-btn"
  );
  const addItemForm = document.getElementById("addItemForm");

  addItemBtn.addEventListener("click", function () {
    addItemModal.style.display = "block";
  });

  closeModalBtns.forEach((btn) =>
    btn.addEventListener("click", function () {
      addItemModal.style.display = "none";
    })
  );

  function validateForm(formData) {
    const itemName = formData.get("item_name");
    const itemPrice = formData.get("item_price");
    const categoryId = formData.get("category_id");

    if (!itemName || !itemPrice || !categoryId) {
      alert("Popunite sva polja.");
      return false;
    }

    const price = parseFloat(itemPrice);
    if (isNaN(price) || price <= 0) {
      alert("Cijena mora biti pozitivan broj.");
      return false;
    }

    return true;
  }

  addItemForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(addItemForm);

    if (!validateForm(formData)) {
      return;
    }

    fetch("add-item.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);

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

          addItemForm.reset();
          addItemModal.style.display = "none";
        } else {
          alert(data.message);
        }
      })
      .catch((error) => {
        alert("Greška, pokušajte ponovno.");
      });
  });
});
