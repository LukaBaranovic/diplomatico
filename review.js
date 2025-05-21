document.addEventListener("DOMContentLoaded", () => {
  const tableRows = document.querySelectorAll("#receiptsTable tbody tr");

  tableRows.forEach((row) => {
    row.addEventListener("click", async () => {
      const receiptId = row.getAttribute("data-receipt-id");

      try {
        const response = await fetch(
          `getReceiptDetails.php?receipt_id=${receiptId}`
        );
        if (!response.ok) {
          throw new Error("Greška pri dohvaćanja detalja računa.");
        }

        const data = await response.json();

        const popup = document.querySelector("#receiptPopup");
        popup.querySelector(".popup-header").innerHTML = `
          <p><strong>ID:</strong> ${data.receipt.receipt_id}</p>
          <p><strong>Broj stola:</strong> ${data.receipt.table_number}</p>
        `;

        const itemsTable = popup.querySelector(".popup-items tbody");
        itemsTable.innerHTML = "";

        data.items.forEach((item) => {
          const row = document.createElement("tr");
          const totalPrice = parseFloat(item.total_price);
          row.innerHTML = `
            <td>${item.item_name}</td>
            <td>${item.quantity}</td>
            <td>${isNaN(totalPrice) ? "0.00" : totalPrice.toFixed(2)} €</td>
          `;
          itemsTable.appendChild(row);
        });

        const totalReceiptPrice = parseFloat(data.receipt.total_price);
        popup.querySelector(".popup-total").textContent = `Ukupna cijena: ${
          isNaN(totalReceiptPrice) ? "0.00" : totalReceiptPrice.toFixed(2)
        } €`;

        popup.style.display = "block";
      } catch (error) {
        alert(error.message);
      }
    });
  });

  document.querySelector("#popupClose").addEventListener("click", () => {
    document.querySelector("#receiptPopup").style.display = "none";
  });

  document.querySelector("#popupCancel").addEventListener("click", () => {
    document.querySelector("#receiptPopup").style.display = "none";
  });

  const dateInput = document.getElementById("dateSelector");
  if (dateInput) {
    dateInput.addEventListener("change", function () {
      dateInput.blur();

      // Uncomment the next line if you want to auto-submit the form as well:
      // dateInput.form.submit();
    });
  }
});
