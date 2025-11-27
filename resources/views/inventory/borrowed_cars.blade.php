@extends('layouts.inventory')

@section('styles')
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; }
.btn-return { background-color:#27ae60; }
.btn-view { background-color:#8e44ad; } /* NEW style for View button */
@endsection

@section('content')
<section>
  <h2>Borrowed Cars</h2>
  <input type="text" id="carSearch" class="search-input" placeholder="Search Cars..." />
  <table id="borrowedCarsTable">
    <thead>
      <tr>
        <th>Borrow ID</th>
        <th>Borrower Name</th>
        <th>Make & Model</th>
        <th>Borrowed Date</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</section>

<!-- Return Car Modal -->
<div id="returnCarModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeReturnCarModal()">&times;</span>
    <h3>Confirm Return</h3>
    <p>Are you sure you want to mark this car as returned?</p>
    <input type="hidden" id="returnCarId">
    <button class="btn btn-return" onclick="confirmReturnCar()">Yes, Return</button>
  </div>
</div>

<!-- View Car Modal -->
<div id="viewCarModal" class="modal">
  <div class="modal-content" style="max-width: 500px;">
    <span class="close" onclick="closeViewCarModal()">&times;</span>
    <h3 style="margin-bottom: 15px;">Borrowed Car Details</h3>
    <table style="width: 100%; border-collapse: collapse;">
      <tbody>
        <tr>
          <th style="text-align: left; padding: 8px;">Borrow ID</th>
          <td style="padding: 8px;" id="viewCarBorrowId"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Borrower Name</th>
          <td style="padding: 8px;" id="viewCarBorrower"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Car</th>
          <td style="padding: 8px;" id="viewCarModel"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Borrowed Date</th>
          <td style="padding: 8px;" id="viewCarBorrowDate"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Due Date</th>
          <td style="padding: 8px;" id="viewCarDueDate"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Status</th>
          <td style="padding: 8px;" id="viewCarStatus"></td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 8px;">Notes</th>
          <td style="padding: 8px;" id="viewCarNotes"></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

@include('inventory.partials.modals')
@endsection

@section('scripts')
async function fetchBorrowedCars(){
  const res = await fetch('{{ url('inventory/api/borrowed-cars') }}');
  const data = await res.json();
  const tbody = document.querySelector('#borrowedCarsTable tbody');
  tbody.innerHTML = '';
  
  data.forEach(bc => {
    const statusClass = bc.status === 'overdue' ? 'overdue' : bc.status === 'returned' ? 'returned' : 'active';
    
    const borrowDate = new Date(bc.borrow_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const dueDate = new Date(bc.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>BORR${bc.id.toString().padStart(3, '0')}</td>
      <td>${bc.borrower_name}</td>
      <td>${bc.car.make_model}</td>
      <td>${borrowDate}</td>
      <td>${dueDate}</td>
      <td class="${statusClass}">${bc.status}</td>
      <td>
        <button class="btn btn-return" onclick="openReturnCarModal(${bc.id})" ${bc.status === 'returned' ? 'disabled' : ''}>Return</button>
        <button class="btn btn-view" onclick='openViewCarModal(${JSON.stringify(bc)})'>View</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

function openReturnCarModal(id){
  document.getElementById('returnCarId').value = id;
  document.getElementById('returnCarModal').style.display = 'flex';
}

function closeReturnCarModal(){
  document.getElementById('returnCarModal').style.display = 'none';
}

async function confirmReturnCar(){
  const id = document.getElementById('returnCarId').value;
  const res = await fetch(`{{ url('inventory/api/borrowed-cars') }}/${id}/return`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  });
  if (res.ok) {
    alert('Car returned successfully!');
    closeReturnCarModal();
    fetchBorrowedCars();
  } else {
    alert('Error returning car');
  }
}

// 🆕 Open View Modal and populate it
function openViewCarModal(carData) {
  document.getElementById('viewCarBorrowId').textContent = `BORR${carData.id.toString().padStart(3, '0')}`;
  document.getElementById('viewCarBorrower').textContent = carData.borrower_name;
  document.getElementById('viewCarModel').textContent = carData.car.make_model;
  document.getElementById('viewCarBorrowDate').textContent = new Date(carData.borrow_date).toLocaleDateString();
  document.getElementById('viewCarDueDate').textContent = new Date(carData.due_date).toLocaleDateString();
  document.getElementById('viewCarStatus').textContent = carData.status;
  document.getElementById('viewCarNotes').textContent = carData.notes || 'No notes.';
  
  document.getElementById('viewCarModal').classList.add('show');
}

// 🆕 Close View Modal
function closeViewCarModal() {
  document.getElementById('viewCarModal').classList.remove('show');
}

document.getElementById('carSearch').addEventListener('input', function(){
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#borrowedCarsTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});

fetchBorrowedCars();
@endsection
