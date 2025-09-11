@extends('layouts.inventory')

@section('styles')
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; }
.btn-extend { background-color:#f39c12; }
.btn-return-confirm { background-color:#4CAF50; }
@endsection

@section('content')
<section>
  <h2>Borrowed Cars List</h2>
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
    <tbody>
      <!-- Data will be loaded dynamically from database -->
    </tbody>
  </table>
</section>

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
    
    // Format dates
    const borrowDate = new Date(bc.borrow_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const dueDate = new Date(bc.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

    const tr = document.createElement('tr');
    tr.innerHTML = `<td>BORR${bc.id.toString().padStart(3, '0')}</td>
      <td>${bc.borrower_name}</td>
      <td>${bc.car.make_model}</td>
      <td>${borrowDate}</td>
      <td>${dueDate}</td>
      <td class="${statusClass}">${bc.status}</td>
      <td>
        <button class="btn btn-return" onclick="returnCar(${bc.id})" ${bc.status === 'returned' ? 'disabled' : ''}>Return</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

async function returnCar(id) {
  if (confirm('Mark this car as returned?')) {
    const res = await fetch(`{{ url('inventory/api/borrowed-cars') }}/${id}/return`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });
    if (res.ok) {
      alert('Car returned successfully!');
      fetchBorrowedCars();
    } else {
      alert('Error returning car');
    }
  }
}

document.getElementById('carSearch').addEventListener('input', function(){
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#borrowedCarsTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});

fetchBorrowedCars();
@endsection



