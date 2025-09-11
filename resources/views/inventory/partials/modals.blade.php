<div id="addItemModal" class="modal">
  <div class="modal-content">
    <span class="close" data-close="addItemModal">&times;</span>
    <h3>Add Item</h3>
    <form id="addItemForm">
      <label>Item Name: <input type="text" name="itemName" required></label><br><br>
      <label>Category: <input type="text" name="category" required></label><br><br>
      <label>Quantity: <input type="number" name="quantity" min="1" required></label><br><br>
      <button type="submit" class="btn btn-borrow">Add Item</button>
    </form>
  </div>
</div>

<div id="addCarModal" class="modal">
  <div class="modal-content">
    <span class="close" data-close="addCarModal">&times;</span>
    <h3>Add Car</h3>
    <form id="addCarForm">
      <label>Make & Model: <input type="text" name="carModel" required></label><br><br>
      <label>Year: <input type="text" name="year" required></label><br><br>
      <label>Status: <input type="text" name="status" required></label><br><br>
      <button type="submit" class="btn btn-borrow">Add Car</button>
    </form>
  </div>
</div>



