document.addEventListener("DOMContentLoaded", () => {
  const btnCategories = document.getElementById("btnCategories");
  const btnItems = document.getElementById("btnItems");
  const categoriesSection = document.getElementById("categoriesSection");
  const itemsSection = document.getElementById("itemsSection");

  btnCategories.addEventListener("click", () => {
    categoriesSection.style.display = "block";
    itemsSection.style.display = "none";

    btnCategories.classList.add("active");
    btnItems.classList.remove("active");
  });

  btnItems.addEventListener("click", () => {
    categoriesSection.style.display = "none";
    itemsSection.style.display = "block";

    btnItems.classList.add("active");
    btnCategories.classList.remove("active");
  });

  const itemDeleteButtons = document.querySelectorAll("#itemTable .delete-btn");
  itemDeleteButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const row = event.target.closest("tr");
      const itemId = row.querySelector("td:first-child").textContent;
      if (confirm(`Je li želite obrisati ovaj artikal: ${itemId}?`)) {
        alert(
          `Delete feature for Item ID: ${itemId} will be implemented here.`
        );
      }
    });
  });

  document
    .getElementById("searchBarCategories")
    .addEventListener("keyup", () => {
      filterTable("categoryTable", "searchBarCategories");
    });

  document.getElementById("searchBarItems").addEventListener("keyup", () => {
    filterTable("itemTable", "searchBarItems");
  });
});

/** 
  @param {string} tableId 
  @param {string} searchBarId 
 */

function filterTable(tableId, searchBarId) {
  const searchValue = document.getElementById(searchBarId).value.toLowerCase();
  const table = document.getElementById(tableId);
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    let match = false;

    for (let j = 0; j < cells.length - 1; j++) {
      if (cells[j].textContent.toLowerCase().includes(searchValue)) {
        match = true;
        break;
      }
    }

    rows[i].style.display = match ? "" : "none";
  }
}
