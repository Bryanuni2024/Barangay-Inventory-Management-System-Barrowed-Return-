@extends('layouts.inventory')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('styles')
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; margin: 2px; }
.btn-extend { background-color:#f39c12; }
.btn-return { background-color:#27ae60; }

/* Modal styles for extend functionality */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal.show {
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background-color: #fefefe !important;
  padding: 20px !important;
  border-radius: 8px !important;
  width: 400px !important;
  max-width: 80% !important;
  position: relative !important;
  margin: 0 !important;
  box-shadow: none !important;
  border: none !important;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  position: absolute;
  right: 15px;
  top: 10px;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
@endsection

@section('content')
<section>
  <h2>Borrowed Items</h2>
  <input type="text" id="itemSearch" class="search-input" placeholder="Search Items..." />
  <table id="borrowedItemsTable">
    <thead>
      <tr>
        <th>Borrow ID</th>
        <th>Item Name</th>
        <th>Borrower</th>
        <th>Date Borrowed</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Quantity</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Data will be loaded dynamically from database -->
    </tbody>
  </table>
</section>

@include('inventory.partials.modals')
@endsection

@section('scripts')
async function fetchBorrowedItems(){
  try {
    const res = await fetch('{{ url('inventory/api/borrowed-items') }}');
    if (!res.ok) {
      throw new Error('Failed to fetch borrowed items');
    }
    const data = await res.json();
    const tbody = document.querySelector('#borrowedItemsTable tbody');
    tbody.innerHTML = '';
    data.forEach(bi => {
      const statusClass = bi.status === 'overdue' ? 'overdue' : bi.status === 'returned' ? 'returned' : 'active';
      
      // Format dates
      const borrowDate = new Date(bi.borrow_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      const dueDate = new Date(bi.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

      tr = document.createElement('tr');
      tr.innerHTML = `<td>BORR${bi.id.toString().padStart(3, '0')}</td>
        <td>${bi.item.name}</td>
        <td>${bi.borrower_name}</td>
        <td>${borrowDate}</td>
        <td>${dueDate}</td>
        <td class="${statusClass}">${bi.status}</td>
        <td>${bi.quantity_borrowed}</td>
        <td>
          <button class="btn btn-extend" onclick="openExtendModal(${bi.id})" ${bi.status === 'returned' ? 'disabled' : ''}>Extend</button>
          <button class="btn btn-return" onclick="returnItem(${bi.id})" ${bi.status === 'returned' ? 'disabled' : ''}>Return</button>
        </td>`;
      tbody.appendChild(tr);
    });
  } catch (error) {
    console.error('Error fetching borrowed items:', error);
    alert('Failed to load borrowed items. Please try again.');
  }
}

async function returnItem(id) {
  if (confirm('Mark this item as returned?')) {
    const res = await fetch(`{{ url('inventory/api/borrowed-items') }}/${id}/return`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });
    if (res.ok) {
      alert('Item returned successfully!');
      fetchBorrowedItems();
    } else {
      alert('Error returning item');
    }
  }
}

// Extend functionality
async function openExtendModal(id) {
  try {
    // Fetch borrow details
    const res = await fetch(`{{ url('inventory/api/borrowed-items') }}/${id}`);
    if (!res.ok) {
      throw new Error('Failed to fetch item details');
    }
    const data = await res.json();
    
    // Create and show modal with date picker
    const modal = document.createElement('div');
    modal.className = 'modal show';
    modal.innerHTML = `
      <div class="modal-content">
        <span class="close" onclick="this.closest('.modal').remove(); document.body.classList.remove('modal-open');">&times;</span>
        <h3>Extend Borrow Period</h3>
        <p>You are extending the borrow period for: <strong>${data.item.name}</strong></p>
        <p>Borrower: <strong>${data.borrower_name}</strong></p>
        <p>Current due date: <strong>${new Date(data.due_date).toLocaleDateString()}</strong></p>
        <div style="margin: 15px 0;">
          <label for="newDueDate">Select New Due Date:</label>
          <input type="date" id="newDueDate" min="${data.due_date.slice(0,10)}" value="${data.due_date.slice(0,10)}" style="padding: 5px; margin-left: 10px;">
        </div>
        <button class="btn btn-extend" onclick="extendItem(${data.id})" style="width: 100%;">Confirm Extension</button>
      </div>
    `;
    
    document.body.appendChild(modal);
    document.body.classList.add('modal-open');
  } catch (error) {
    console.error('Error opening extend modal:', error);
    alert('Failed to load item details. Please try again.');
  }
}

function calculateNewDueDate(currentDueDate) {
  const extensionDays = parseInt(document.getElementById('extensionDays').value);
  const newDueDate = new Date(currentDueDate);
  newDueDate.setDate(newDueDate.getDate() + extensionDays);
  document.getElementById('newDueDate').value = newDueDate.toLocaleDateString();
}

async function extendItem(id) {
  const newDueDate = document.getElementById('newDueDate').value;
  if (!newDueDate) {
    alert('Please select a new due date.');
    return;
  }
  try {
    const res = await fetch(`{{ url('inventory/api/borrowed-items') }}/${id}/extend`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        new_due_date: newDueDate
      })
    });
    const result = await res.json();
    if (res.ok) {
      alert('Borrow period extended successfully!');
      // Remove only the modal related to this button
      const btn = document.activeElement;
      const modal = btn.closest('.modal');
      if (modal) modal.remove();
      document.body.classList.remove('modal-open');
      fetchBorrowedItems();
    } else {
      alert('Error: ' + (result.error || 'Failed to extend borrow period'));
    }
  } catch (error) {
    console.error('Error extending item:', error);
    alert('Failed to extend borrow period. Please try again.');
  }
}

document.getElementById('itemSearch').addEventListener('input', function(){
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#borrowedItemsTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});

fetchBorrowedItems();
@endsection