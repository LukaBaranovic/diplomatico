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
