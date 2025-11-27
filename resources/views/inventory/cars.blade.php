@extends('layouts.inventory')

@section('styles')
/* Table & buttons */
table { border-collapse: collapse; width:100%; margin-top:15px; }
th, td { border:1px solid #ddd; padding:8px; text-align:center; }
.table-actions { display:flex; justify-content:space-between; align-items:center; margin-top:15px; margin-bottom:10px; }
.search-input { padding:7px 12px; border:1px solid #ccc; border-radius:5px; font-size:15px; width:200px; }
.btn { padding:5px 10px; border:none; border-radius:4px; cursor:pointer; font-size:14px; color:white; }
.btn-delete { background-color:#e74c3c; }
.btn-borrow { background-color:#2e86c1; }
.btn-edit { background-color:#4CAF50; }

/* Modal styling handled in style.css */
@endsection

@section('content')
<section>
  <h2>Car List</h2>
  <div class="table-actions">
    <button onclick="openModal('addCarModal')" class="btn btn-borrow">Add Car</button>
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
<!-- Add Car Modal -->
<div id="addCarModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('addCarModal')">&times;</span>
    <h3>Add Car</h3>
    <form method="POST" action="{{ url('inventory/api/cars') }}">
      @csrf
      <label>Make & Model:
        <input type="text" name="make_model" required>
      </label>
      <label>Year:
        <input type="text" name="year">
      </label>
      <input type="hidden" name="status" value="Available">
      <button type="submit" class="btn btn-borrow" style="margin-left: 40px">Add Car</button>
    </form>
  </div>
</div>

<!-- Edit Car Modal -->
<div id="editCarModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('editCarModal')">&times;</span>
    <h3>Edit Car</h3>
    <form id="editCarForm">
      <input type="hidden" id="editCarId">
      <label> <p style="margin-left: 35px">Make & Model:</p>
        <input type="text" id="editMakeModel" required>
      </label>
      <label> <p style="margin-left: 35px"> Year: </p>
        <div>
        <input type="text" id="editYear">
        </div>
      </label>
      <label><p style="margin-left: 35px">Status:</p>
        <div style="margin-left: 35px">
        <select id="editStatus" required style="padding: 8px">
          <option value="Available">Available</option>
          <option value="Under Maintenance">Under Maintenance</option>
        </select>
        </div>
      </label>
      <p id="borrowedNotice" style="color:red; display:none; font-size:13px;">
        ⚠ This car is currently borrowed. Status cannot be changed until it is returned.
      </p>
      <button type="submit" class="btn btn-edit" style="margin-left: 45px; margin-top: 60px;">Save</button>
    </form>
  </div>
</div>

<!-- Borrow Car Modal -->
<div id="borrowCarModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('borrowCarModal')">&times;</span>
    <h3>Borrow Car</h3>
    <form id="borrowCarForm">
      <input type="hidden" id="borrowCarId" name="car_id">
      <div style="margin-bottom: 15px;">
      <label style="margin-left: 25px">Car: <span id="borrowCarName" style="font-weight: bold; color: rgb(255, 99, 71);" ></span></label>
      </div>
      <div style="margin-bottom: 15px;">
      <label style="margin-left: 25px">Borrower Name:<input type="text" name="borrower_name" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
      <label style="margin-left: 25px">Borrow Date:<input type="date" name="borrow_date" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
      <label style="margin-left: 25px">Due Date:<input type="date" name="due_date" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
      <label style="margin-left: 25px">Notes: <br><textarea name="notes" style="width: 96%; padding: 8px; margin-top: 5px; height: 60px;"></textarea></label>
      </div>
      <button type="submit" class="btn btn-borrow">Borrow Car</button>
    </form>
  </div>
</div>
@endsection

@section('scripts')
function openModal(id){
  document.getElementById(id).classList.add('show');
  document.body.classList.add('modal-open');
}
function closeModal(id){
  document.getElementById(id).classList.remove('show');
  document.body.classList.remove('modal-open');
}

async function fetchCars(){
  const res = await fetch('{{ url("inventory/api/cars") }}');
  const data = await res.json();
  const tbody = document.querySelector('#carTable tbody');
  tbody.innerHTML = '';

  data.sort((a, b) => a.id - b.id);

  data.forEach(c => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${c.code}</td>
                    <td>${c.make_model}</td>
                    <td>${c.year||''}</td>
                    <td>${c.status}</td>
      <td style="display:flex; gap:6px; justify-content:center;">
        <button class="btn btn-edit" data-id="${c.id}">Edit</button>
        <form method="POST" action="{{ url('inventory/api/cars') }}/${c.id}" onsubmit="return confirm('Delete car?')">
          <input type="hidden" name="_method" value="DELETE">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button class="btn btn-delete" type="submit">Delete</button>
        </form>
        <button class="btn btn-borrow" data-id="${c.id}">Borrow</button>
      </td>`;
    tbody.appendChild(tr);

    // Edit button
    tr.querySelector('.btn-edit').addEventListener('click', () => {
      document.getElementById('editCarId').value = c.id;
      document.getElementById('editMakeModel').value = c.make_model;
      document.getElementById('editYear').value = c.year || '';
      document.getElementById('editStatus').value = c.status;

      const statusField = document.getElementById('editStatus');
      const notice = document.getElementById('borrowedNotice');

      if (c.status === 'Borrowed') {
        statusField.disabled = true;
        notice.style.display = 'block';
      } else {
        statusField.disabled = false;
        notice.style.display = 'none';
      }

      openModal('editCarModal');
    });

    // Borrow button
    tr.querySelector('.btn-borrow').addEventListener('click', () => {
      if (c.status === 'Borrowed') {
        alert('This car is already borrowed!');
        return;
      }
      document.getElementById('borrowCarId').value = c.id;
      document.getElementById('borrowCarName').textContent = c.make_model;
      const today = new Date().toISOString().split('T')[0];
      const nextWeek = new Date(Date.now() + 7*24*60*60*1000).toISOString().split('T')[0];
      document.querySelector('#borrowCarForm input[name="borrow_date"]').value = today;
      document.querySelector('#borrowCarForm input[name="due_date"]').value = nextWeek;
      openModal('borrowCarModal');
    });
  });
}

// Edit Car Form
document.getElementById('editCarForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const carId = document.getElementById('editCarId').value;
  const makeModel = document.getElementById('editMakeModel').value;
  const year = document.getElementById('editYear').value;
  const statusField = document.getElementById('editStatus');

  // If borrowed, block update of status
  if (statusField.disabled) {
    try {
      const res = await fetch(`{{ url("inventory/api/cars") }}/${carId}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({_method:'PUT', make_model:makeModel, year})
      });
      if(res.ok){
        alert('Car details updated (status unchanged).');
        closeModal('editCarModal');
        fetchCars();
      }
    } catch(err){ alert('Error updating car: '+err.message); }
    return;
  }

  const status = statusField.value;
  try {
    const res = await fetch(`{{ url("inventory/api/cars") }}/${carId}`, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body:JSON.stringify({_method:'PUT', make_model:makeModel, year, status})
    });
    if(res.ok){
      alert('Car updated successfully!');
      closeModal('editCarModal');
      fetchCars();
    } else {
      const error = await res.json();
      alert('Error: '+(error.error||'Failed to update car'));
    }
  } catch(err){ alert('Error updating car: '+err.message); }
});

// Borrow Car Form
document.getElementById('borrowCarForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(this).entries());
  try {
    const res = await fetch('{{ url("inventory/api/borrowed-cars") }}',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body:JSON.stringify({
        car_id: parseInt(data.car_id),
        borrower_name: data.borrower_name,
        borrow_date: data.borrow_date,
        due_date: data.due_date,
        notes: data.notes
      })
    });
    if(res.ok){
      alert('Car borrowed successfully!');
      closeModal('borrowCarModal');
      this.reset();
      fetchCars();
    } else {
      const error = await res.json();
      alert('Error: '+(error.error||'Failed to borrow car'));
    }
  } catch(err){ alert('Error borrowing car: '+err.message); }
});

document.getElementById('carSearch').addEventListener('input', function(){
  const filter = this.value.toLowerCase();
  document.querySelectorAll('#carTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter)?'':'none';
  });
});

fetchCars();
@endsection
