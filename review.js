// Wait for the DOM to load
document.addEventListener("DOMContentLoaded", () => {
  const tableRows = document.querySelectorAll("#receiptsTable tbody tr");

  // Add click event for each table row
  tableRows.forEach((row) => {
    row.addEventListener("click", () => {
      const receiptId = row.getAttribute("data-receipt-id");
      alert(
        `Receipt ID: ${receiptId}\nDetails functionality will be implemented later.`
      );
    });
  });
});

// Wait for the DOM to load
document.addEventListener("DOMContentLoaded", () => {
  const dateSelector = document.getElementById("dateSelector");
  const tableBody = document.querySelector("#receiptsTable tbody");

  // Function to fetch and update receipts based on the selected date
  const fetchReceipts = (date) => {
    // Send AJAX POST request to the server
    $.ajax({
      url: "review.php", // Make request to review.php
      method: "POST",
      data: { date: date },
      dataType: "json",
      success: (receipts) => {
        // Clear the table body
        tableBody.innerHTML = "";

        // Populate the table with new data
        receipts.forEach((receipt) => {
          const row = document.createElement("tr");
          row.setAttribute("data-receipt-id", receipt.receipt_id);

          row.innerHTML = `
                      <td>${receipt.receipt_id}</td>
                      <td>${receipt.table_number}</td>
                      <td>${receipt.total_price}</td>
                      <td>${receipt.timestamp}</td>
                  `;

          // Add click event to the row
          row.addEventListener("click", () => {
            alert(
              `Receipt ID: ${receipt.receipt_id}\nDetails functionality will be implemented later.`
            );
          });

          tableBody.appendChild(row);
        });
      },
      error: (xhr, status, error) => {
        console.error("Error fetching receipts:", error);
        alert("Failed to fetch receipts. Please try again.");
      },
    });
  };

  // Event listener for the date selector
  dateSelector.addEventListener("change", (event) => {
    const selectedDate = event.target.value;
    fetchReceipts(selectedDate);
  });
});
