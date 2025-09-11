// Show the relevant section on the page
function showSection(id) {
  document.querySelectorAll('.section').forEach(div => div.style.display = 'none');
  document.getElementById(id).style.display = 'block';
}

// Go back to the dashboard
function goBack() {
  showSection('dashboard');
}

// Search for items in the item list
function searchItem() {
  let f = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#itemTable tbody tr').forEach(r => {
    r.style.display = r.cells[0].innerText.toLowerCase().includes(f) ? '' : 'none';
  });
}

// Open the Edit Modal and populate it with the current item data
function openEditModal(id, name, quantity, location, category) {
  // Set the values of the input fields
  document.getElementById('editItemId').value = id; // Set item ID
  document.getElementById('editName').value = name; // Set item Name
  document.getElementById('editQuantity').value = quantity; // Set item Quantity
  document.getElementById('editLocation').value = location; // Set item Location
  document.getElementById('editCategory').value = category; // Set item Category
  
  // Show the modal
  document.getElementById('editItemModal').style.display = 'flex';
}

// Close the Edit Modal
function closeEditItemModal() {
  document.getElementById('editItemModal').style.display = 'none';
}

// Open the Borrow Modal and populate it with the item details
function openBorrowModal(id, maxQty) {
  // Create the modal content dynamically
  const modal = document.createElement('div');
  modal.classList.add('modal');
  modal.innerHTML = `
      <div class="modal-content">
          <h3>Borrow Item</h3>
          <form id="borrowForm" method="POST">
              <input type="hidden" name="item_id" value="${id}">
              <label>Borrower's Name:</label>
              <input name="borrower" required>
              <label>Quantity (Max: ${maxQty}):</label>
              <input type="number" name="quantity" max="${maxQty}" min="1" required>
              <input type="hidden" name="max_quantity" value="${maxQty}">
              <input type="hidden" name="item_name" id="itemNameHidden">
              <button type="submit">Borrow</button>
              <button type="button" onclick="closeModal()">Cancel</button>
          </form>
      </div>
  `;
  
  // Add the modal content to the container
  const modalContainer = document.getElementById('modalContainer');
  modalContainer.innerHTML = '';
  modalContainer.appendChild(modal);
  modal.style.display = 'block';

  // Find the item name in the table row using the item id
  const itemRow = [...document.querySelectorAll('#itemTable tbody tr')].find(row =>
    row.querySelector('button[onclick^="openBorrowModal"]').getAttribute('onclick').includes(`${id},`)
  );
  const itemName = itemRow ? itemRow.querySelector('td').innerText.trim() : '';
  
  // Set the hidden input field for the item name
  modal.querySelector('#itemNameHidden').value = itemName;
}

// Handle the form submission for borrowing an item
document.getElementById('modalContainer').addEventListener('submit', function(e) {
  if (e.target.id === 'borrowForm') {
    e.preventDefault(); // Prevent default form submission

    const itemId = e.target.item_id.value;
    const quantity = parseInt(e.target.quantity.value);
    const maxQuantity = parseInt(e.target.max_quantity.value);

    if (quantity <= maxQuantity) {
      // Now deduct the quantity in the frontend (dynamic update)
      const itemRow = [...document.querySelectorAll('#itemTable tbody tr')].find(row =>
        row.querySelector('button[onclick^="openBorrowModal"]').getAttribute('onclick').includes(`${itemId},`)
      );
      const currentQtyCell = itemRow.querySelector('td:nth-child(3)'); // Assuming quantity is in the 3rd column
      let currentQty = parseInt(currentQtyCell.innerText);

      // Deduct the borrowed quantity
      currentQty -= quantity;
      currentQtyCell.innerText = currentQty;

      // Send the request to the server to update the quantity in the database
      fetch('borrow_item.php', {
        method: 'POST',
        body: new FormData(e.target)
      }).then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Item borrowed successfully');
            closeModal();
          } else {
            alert('Failed to borrow item');
          }
        })
        .catch(err => alert('Error: ' + err));
    } else {
      alert('Invalid quantity!');
    }
  }
});

// Confirm item deletion
function confirmDelete(id) {
  if (confirm('Delete this item?')) {
    window.location = 'delete_item.php?id=' + id;
  }
}

// Close any open modal
function closeModal() {
  document.getElementById('modalContainer').style.display = 'none';
}

// Close the Borrow Item Modal
function closeBorrowModal() {
  document.getElementById('borrowItemModal').style.display = 'none';
}
function openAddCarModal() {
  document.getElementById('addCarModal').style.display = 'flex';
}
function closeAddCarModal() {
  document.getElementById('addCarModal').style.display = 'none';
}
window.onclick = function(e) {
  if (e.target === document.getElementById('addItemModal')) closeAddItemModal();
  if (e.target === document.getElementById('addCarModal')) closeAddCarModal();
};
function openAddCarModal() {
  document.getElementById('addCarModal').style.display = 'flex';
}
function closeAddCarModal() {
  document.getElementById('addCarModal').style.display = 'none';
}

function openEditCarModal(id, model, plate, status) {
  document.getElementById('editCarId').value = id;
  document.getElementById('editCarModel').value = model;
  document.getElementById('editPlateNumber').value = plate;
  document.getElementById('editCarStatus').value = status;
  document.getElementById('editCarModal').style.display = 'flex';
}
function closeEditCarModal() {
  document.getElementById('editCarModal').style.display = 'none';
}

function openBorrowCarModal(id) {
  document.getElementById('borrowCarId').value = id;
  document.getElementById('borrowCarModal').style.display = 'flex';
}
function closeBorrowCarModal() {
  document.getElementById('borrowCarModal').style.display = 'none';
}

function confirmDeleteCar(id) {
  if (confirm("Are you sure you want to delete this car?")) {
    window.location.href = 'delete_car.php?id=' + id;
  }
}

function searchCar() {
  const input = document.getElementById('carSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#carTable tbody tr');
  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
  });
}
