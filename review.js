// Wait for the DOM to load
document.addEventListener("DOMContentLoaded", () => {
  const tableRows = document.querySelectorAll("#receiptsTable tbody tr");

  // Add click event for each table row
  tableRows.forEach((row) => {
    row.addEventListener("click", async () => {
      const receiptId = row.getAttribute("data-receipt-id");

      try {
        // Fetch receipt details from the server
        const response = await fetch(
          `getReceiptDetails.php?receipt_id=${receiptId}`
        );
        if (!response.ok) {
          throw new Error("Failed to fetch receipt details.");
        }

        const data = await response.json();

        // Populate the popup with receipt details
        const popup = document.querySelector("#receiptPopup");
        popup.querySelector(
          ".popup-header"
        ).textContent = `Receipt ID: ${data.receipt.receipt_id} | Table Number: ${data.receipt.table_number}`;

        const itemsTable = popup.querySelector(".popup-items tbody");
        itemsTable.innerHTML = ""; // Clear previous items

        data.items.forEach((item) => {
          const row = document.createElement("tr");
          const totalPrice = parseFloat(item.total_price); // Ensure total_price is a number
          row.innerHTML = `
            <td>${item.item_name}</td>
            <td>${item.quantity}</td>
            <td>${isNaN(totalPrice) ? "0.00" : totalPrice.toFixed(2)}</td>
          `;
          itemsTable.appendChild(row);
        });

        const totalReceiptPrice = parseFloat(data.receipt.total_price); // Ensure total_price is a number
        popup.querySelector(".popup-total").textContent = `Total Price: ${
          isNaN(totalReceiptPrice) ? "0.00" : totalReceiptPrice.toFixed(2)
        }`;

        // Show the popup
        popup.style.display = "block";
      } catch (error) {
        alert(error.message);
      }
    });
  });

  // Close popup handler for the "X" button
  document.querySelector("#popupClose").addEventListener("click", () => {
    document.querySelector("#receiptPopup").style.display = "none";
  });

  // Close popup handler for the "Otkaži" button
  document.querySelector("#popupCancel").addEventListener("click", () => {
    document.querySelector("#receiptPopup").style.display = "none";
  });
});
