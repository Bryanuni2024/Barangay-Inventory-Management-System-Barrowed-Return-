@extends('layouts.inventory')

@section('styles')
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.table-actions { display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:10px; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; }
.btn-delete { background-color:#e74c3c; }
.btn-borrow { background-color:#2e86c1; }
.btn-edit { background-color:#4CAF50; }
@endsection

@section('content')
<section>
  <h2>Car List</h2>
  <div class="table-actions">
    <button onclick="document.getElementById('addCarModal').classList.add('show'); document.body.classList.add('modal-open');" class="btn btn-borrow">Add Car</button>
    <input type="text" id="carSearch" class="search-input" placeholder="Search Cars..." />
  </div>
  <table id="carTable">
    <thead>
      <tr>
        <th>Car ID</th>
        <th>Make & Model</th>
        <th>Year</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</section>

@endsection

@section('modals')
<div id="addCarModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add Car</h3>
    <form method="POST" action="{{ url('inventory/api/cars') }}">
      @csrf
      <div style="margin-bottom: 15px;">
        <label>Make & Model: <input type="text" name="make_model" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Year: <input type="text" name="year" style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <!-- Status field removed as per new requirements -->
      <button type="submit" class="btn btn-borrow">Add Car</button>
    </form>
  </div>
</div>

<!-- Edit Car Modal -->
<div id="editCarModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Edit Car</h3>
    <form id="editCarForm">
      <input type="hidden" name="car_id" id="editCarId">
      <div style="margin-bottom: 15px;">
        <label>Make & Model: <input type="text" name="make_model" id="editMakeModel" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Year: <input type="text" name="year" id="editYear" style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <button type="submit" class="btn btn-edit">Save</button>
    </form>
  </div>
</div>

<!-- Borrow Car Modal -->
<div id="borrowCarModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Borrow Car</h3>
    <form id="borrowCarForm">
      <div style="margin-bottom: 15px;">
        <label>Car: <span id="borrowCarName" style="font-weight: bold;"></span></label>
        <input type="hidden" id="borrowCarId" name="car_id">
      </div>
      <div style="margin-bottom: 15px;">
        <label>Borrower Name: <input type="text" name="borrower_name" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Borrow Date: <input type="date" name="borrow_date" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Due Date: <input type="date" name="due_date" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Notes: <textarea name="notes" style="width: 100%; padding: 8px; margin-top: 5px; height: 60px;"></textarea></label>
      </div>
      <button type="submit" class="btn btn-borrow">Borrow Car</button>
    </form>
  </div>
</div>
@endsection

@section('scripts')
async function fetchCars(){
  const res = await fetch('{{ url('inventory/api/cars') }}');
  const data = await res.json();
  const tbody = document.querySelector('#carTable tbody');
  tbody.innerHTML = '';
  data.forEach(c => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${c.code}</td><td>${c.make_model}</td><td>${c.year||''}</td><td>${c.status}</td>
      <td style="display:flex; gap:6px; justify-content:center;">
        <button type="button" class="btn btn-edit" data-id="${c.id}">Edit</button>
        <form method="POST" action="{{ url('inventory/api/cars') }}/${c.id}" onsubmit="return confirm('Delete car?')">
          <input type="hidden" name="_method" value="DELETE">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button class="btn btn-delete" type="submit">Delete</button>
        </form>
        <button type="button" class="btn btn-borrow" data-id="${c.id}">Borrow</button>
      </td>`;
    tbody.appendChild(tr);

    const editBtn = tr.querySelector('.btn-edit');
    editBtn.addEventListener('click', () => {
      // Fill modal with car data
      document.getElementById('editCarId').value = c.id;
      document.getElementById('editMakeModel').value = c.make_model;
      document.getElementById('editYear').value = c.year || '';
      document.getElementById('editCarModal').classList.add('show');
      document.body.classList.add('modal-open');
    });

    const borrowBtn = tr.querySelector('.btn-borrow');
    borrowBtn.addEventListener('click', () => {
      const carId = borrowBtn.dataset.id;
      document.getElementById('borrowCarId').value = carId;
      document.getElementById('borrowCarName').textContent = c.make_model;
      const today = new Date().toISOString().split('T')[0];
      const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
      document.querySelector('#borrowCarForm input[name="borrow_date"]').value = today;
      document.querySelector('#borrowCarForm input[name="due_date"]').value = nextWeek;
      document.getElementById('borrowCarModal').classList.add('show');
      document.body.classList.add('modal-open');
    });
  });
}

document.getElementById('carSearch').addEventListener('input', function(){
  const filter=this.value.toLowerCase();
  document.querySelectorAll('#carTable tbody tr').forEach(row=>{
    row.style.display = row.textContent.toLowerCase().includes(filter)?'':'none';
  });
});

// Handle edit car form submission
const editCarForm = document.getElementById('editCarForm');
editCarForm.addEventListener('submit', async function(e) {
  e.preventDefault();
  const carId = document.getElementById('editCarId').value;
  const makeModel = document.getElementById('editMakeModel').value;
  const year = document.getElementById('editYear').value;
  try {
    const res = await fetch(`{{ url('inventory/api/cars') }}/${carId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      body: JSON.stringify({
        _method: 'PUT',
        make_model: makeModel,
        year: year
      })
    });
    if (res.ok) {
      alert('Car updated successfully!');
      document.getElementById('editCarModal').classList.remove('show');
      document.body.classList.remove('modal-open');
      fetchCars();
    } else {
      const error = await res.json();
      alert('Error: ' + (error.error || 'Failed to update car'));
    }
  } catch (error) {
    alert('Error updating car: ' + error.message);
  }
});

// Handle modal close buttons
Array.from(document.querySelectorAll('.modal .close')).forEach(btn => {
  btn.onclick = function() {
    btn.closest('.modal').classList.remove('show');
    document.body.classList.remove('modal-open');
  };
});

// Handle borrow car form submission
document.getElementById('borrowCarForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = Object.fromEntries(formData.entries());
  
  try {
    const res = await fetch('{{ url('inventory/api/borrowed-cars') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        car_id: parseInt(data.car_id),
        borrower_name: data.borrower_name,
        borrow_date: data.borrow_date,
        due_date: data.due_date,
        notes: data.notes
      })
    });
    
    if (res.ok) {
      alert('Car borrowed successfully!');
      document.getElementById('borrowCarModal').classList.remove('show');
      document.body.classList.remove('modal-open');
      this.reset();
      fetchCars(); // Refresh the cars list
    } else {
      const error = await res.json();
      alert('Error: ' + (error.error || 'Failed to borrow car'));
    }
  } catch (error) {
    alert('Error borrowing car: ' + error.message);
  }
});

fetchCars();
@endsection


