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
@section('modals')
<div id="addItemModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add Item</h3>
    <form method="POST" action="{{ url('inventory/api/items') }}">
      @csrf
      <div style="margin-bottom: 15px;">
        <label>Item Name: <input type="text" name="name" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Category: <input type="text" name="category" style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Quantity: <input type="number" name="quantity" min="0" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <button type="submit" class="btn btn-borrow">Add Item</button>
    </form>
  </div>
</div>

<!-- Edit Item Modal -->
<div id="editItemModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Edit Item</h3>
    <form id="editItemForm">
      <input type="hidden" name="item_id" id="editItemId">
      <div style="margin-bottom: 15px;">
        <label>Item Name: <input type="text" name="name" id="editItemName" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Category: <input type="text" name="category" id="editItemCategory" style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Quantity: <input type="number" name="quantity" id="editItemQuantity" min="0" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <button type="submit" class="btn btn-edit">Save</button>
    </form>
  </div>
</div>

<!-- Borrow Item Modal -->
<div id="borrowItemModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Borrow Item</h3>
    <form id="borrowItemForm">
      <div style="margin-bottom: 15px;">
        <label>Item: <span id="borrowItemName" style="font-weight: bold;"></span></label>
        <input type="hidden" id="borrowItemId" name="item_id">
      </div>
      <div style="margin-bottom: 15px;">
        <label>Borrower Name: <input type="text" name="borrower_name" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
      </div>
      <div style="margin-bottom: 15px;">
        <label>Quantity to Borrow: <input type="number" id="borrowQuantity" name="quantity_borrowed" min="1" required style="width: 100%; padding: 8px; margin-top: 5px;"></label>
        <small style="color: #666;">Available: <span id="availableQuantity"></span></small>
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
      <button type="submit" class="btn btn-borrow">Borrow Item</button>
    </form>
  </div>
</div>
@endsection
<section>
  <h2>Item List</h2>
  <div class="table-actions">
    <button onclick="document.getElementById('addItemModal').classList.add('show'); document.body.classList.add('modal-open');" class="btn btn-borrow">Add Item</button>
    <input type="text" id="itemSearch" class="search-input" placeholder="Search Items..." />
  </div>
  <table id="itemTable">
    <thead>
      <tr>
        <th>Item ID</th>
        <th>Name</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</section>

@endsection



@section('scripts')
async function fetchItems(){
  const res = await fetch('{{ url('inventory/api/items') }}');
  const data = await res.json();
  const tbody = document.querySelector('#itemTable tbody');
  tbody.innerHTML = '';
  data.forEach(it => {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${it.code}</td><td>${it.name}</td><td>${it.category||''}</td><td>${it.quantity}</td>
      <td style="display:flex; gap:6px; justify-content:center;">
        <button type="button" class="btn btn-edit" data-id="${it.id}">Edit</button>
        <form method="POST" action="{{ url('inventory/api/items') }}/${it.id}" onsubmit="return confirm('Delete item?')">
          <input type="hidden" name="_method" value="DELETE">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button class="btn btn-delete" type="submit">Delete</button>
        </form>
        <button type="button" class="btn btn-borrow" data-id="${it.id}" data-quantity="${it.quantity}">Borrow</button>
      </td>`;
    tbody.appendChild(tr);

    const editBtn = tr.querySelector('.btn-edit');
    editBtn.addEventListener('click', () => {
      document.getElementById('editItemId').value = it.id;
      document.getElementById('editItemName').value = it.name;
      document.getElementById('editItemCategory').value = it.category || '';
      document.getElementById('editItemQuantity').value = it.quantity;
      document.getElementById('editItemModal').classList.add('show');
      document.body.classList.add('modal-open');
    });

    const borrowBtn = tr.querySelector('.btn-borrow');
    borrowBtn.addEventListener('click', () => {
      const itemId = borrowBtn.dataset.id;
      document.getElementById('borrowItemId').value = itemId;
      document.getElementById('borrowItemName').textContent = it.name;
      document.getElementById('availableQuantity').textContent = it.quantity;
      const today = new Date().toISOString().split('T')[0];
      const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
      document.querySelector('#borrowItemForm input[name="borrow_date"]').value = today;
      document.querySelector('#borrowItemForm input[name="due_date"]').value = nextWeek;
      document.getElementById('borrowItemModal').classList.add('show');
      document.body.classList.add('modal-open');
    });
  });
}

// Handle edit item form submission
const editItemForm = document.getElementById('editItemForm');
editItemForm.addEventListener('submit', async function(e) {
  e.preventDefault();
  const itemId = document.getElementById('editItemId').value;
  const name = document.getElementById('editItemName').value;
  const category = document.getElementById('editItemCategory').value;
  const quantity = document.getElementById('editItemQuantity').value;
  try {
    const res = await fetch(`{{ url('inventory/api/items') }}/${itemId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      body: JSON.stringify({
        _method: 'PUT',
        name: name,
        category: category,
        quantity: quantity
      })
    });
    if (res.ok) {
      alert('Item updated successfully!');
      document.getElementById('editItemModal').classList.remove('show');
      document.body.classList.remove('modal-open');
      fetchItems();
    } else {
      const error = await res.json();
      alert('Error: ' + (error.error || 'Failed to update item'));
    }
  } catch (error) {
    alert('Error updating item: ' + error.message);
  }
});

// Handle modal close buttons
Array.from(document.querySelectorAll('.modal .close')).forEach(btn => {
  btn.onclick = function() {
    btn.closest('.modal').classList.remove('show');
    document.body.classList.remove('modal-open');
  };
});

document.getElementById('itemSearch').addEventListener('input', function(){
  const filter=this.value.toLowerCase();
  document.querySelectorAll('#itemTable tbody tr').forEach(row=>{
    row.style.display = row.textContent.toLowerCase().includes(filter)?'':'none';
  });
});

// Handle borrow form submission
document.getElementById('borrowItemForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = Object.fromEntries(formData.entries());
  
  try {
    const res = await fetch('{{ url('inventory/api/borrowed-items') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        item_id: parseInt(data.item_id),
        borrower_name: data.borrower_name,
        quantity_borrowed: parseInt(data.quantity_borrowed),
        borrow_date: data.borrow_date,
        due_date: data.due_date,
        notes: data.notes
      })
    });
    
    if (res.ok) {
      alert('Item borrowed successfully!');
      document.getElementById('borrowItemModal').classList.remove('show');
      document.body.classList.remove('modal-open');
      this.reset();
      fetchItems(); // Refresh the items list
    } else {
      const error = await res.json();
      alert('Error: ' + (error.error || 'Failed to borrow item'));
    }
  } catch (error) {
    alert('Error borrowing item: ' + error.message);
  }
});

fetchItems();
@endsection


