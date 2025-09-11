// This script handles the borrow car modal and form submission for borrowed_cars.blade.php

document.addEventListener('DOMContentLoaded', function() {
  // Open borrow car modal
  document.body.addEventListener('click', function(e) {
    if (e.target.matches('.btn-borrow-car')) {
      const carId = e.target.getAttribute('data-car-id');
      const carName = e.target.getAttribute('data-car-name');
      document.getElementById('borrowCarId').value = carId;
      document.getElementById('borrowCarName').textContent = carName;
      document.getElementById('borrowCarModal').classList.add('show');
      document.body.classList.add('modal-open');
    }
    if (e.target.matches('#borrowCarModal .close')) {
      document.getElementById('borrowCarModal').classList.remove('show');
      document.body.classList.remove('modal-open');
    }
  });

  // Handle borrow car form submission
  const borrowCarForm = document.getElementById('borrowCarForm');
  if (borrowCarForm) {
    borrowCarForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      try {
        const res = await fetch('/inventory/api/borrowed-cars', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
          if (typeof fetchBorrowedCars === 'function') fetchBorrowedCars();
        } else {
          const error = await res.json();
          alert('Error: ' + (error.error || 'Failed to borrow car'));
        }
      } catch (error) {
        alert('Error borrowing car: ' + error.message);
      }
    });
  }
});
