@extends('layouts.inventory')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('styles')
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; margin: 2px; }
.btn-extend { background-color:#f39c12; }
.btn-return { background-color:#27ae60; }
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

<!-- Extend Modal -->
<div id="extendModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeExtendModal()">&times;</span>
    <h3>Extend Borrow Period</h3>
    <form id="extendForm">
      <input type="hidden" id="extendItemId">
      <label>Current Due Date:</label>
      <input type="text" id="currentDueDate" readonly class="form-control"><br>
      
      <label>Extension Days:</label>
      <input type="number" id="extensionDays" value="1" min="1" onchange="calculateNewDueDate(document.getElementById('currentDueDate').value)"><br>
      
      <label>New Due Date:</label>
      <input type="text" id="newDueDate" readonly class="form-control"><br>
      
      <button type="button" class="btn btn-extend" onclick="extendItem(document.getElementById('extendItemId').value)">Confirm Extend</button>
    </form>
  </div>
</div>

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

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>BORR${bi.id.toString().padStart(3, '0')}</td>
        <td>${bi.item.name}</td>
        <td>${bi.borrower_name}</td>
        <td>${borrowDate}</td>
        <td>${dueDate}</td>
        <td class="${statusClass}">${bi.status}</td>
        <td>${bi.quantity_borrowed}</td>
        <td>
          <button class="btn btn-extend" onclick="openExtendModal(${bi.id}, '${bi.due_date}')" ${bi.status === 'returned' ? 'disabled' : ''}>Extend</button>
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
      closeExtendModal();
      fetchBorrowedItems();
    } else {
      alert('Error: ' + (result.error || 'Failed to extend borrow period'));
    }
  } catch (error) {
    console.error('Error extending item:', error);
    alert('Failed to extend borrow period. Please try again.');
  }
}

function openExtendModal(id, currentDueDate) {
  document.getElementById('extendItemId').value = id;
  document.getElementById('currentDueDate').value = new Date(currentDueDate).toLocaleDateString();
  document.getElementById('extensionDays').value = 1;
  calculateNewDueDate(currentDueDate);
  document.getElementById('extendModal').classList.add('show');
}
function closeExtendModal() {
  document.getElementById('extendModal').classList.remove('show');
}

document.getElementById('itemSearch').addEventListener('input', function(){
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#borrowedItemsTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});

fetchBorrowedItems();
@endsection
