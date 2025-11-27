@extends('layouts.inventory')

@section('content')
<section>
  <h2 style="text-align: center; color: #2c3e50;">Reports</h2>
  
  <!-- Report Summary Boxes -->
  <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px 0;">
    <div style="flex: 1 1 200px; max-width: 150px; background: #27ae60; color: white; padding: 20px; border-radius: 10px; text-align: center;">
      <h3 style="margin: 0; font-size: 28px;">{{ $items->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Total Items</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #2980b9; color: white; padding: 20px; border-radius: 10px; text-align: center;">
      <h3 style="margin: 0; font-size: 28px;">{{ $cars->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Total Cars</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #f39c12; color: white; padding: 20px; border-radius: 10px; text-align: center;">
      <h3 style="margin: 0; font-size: 28px;">{{ $borrowedItems->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Borrowed Items</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #15c8ff; color: white; padding: 20px; border-radius: 10px; text-align: center;">
      <h3 style="margin: 0; font-size: 28px;">{{ $borrowedCars->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Borrowed Cars</p>
    </div>
  </div>

  <!-- Report Actions -->
  <div class="report-actions" style="margin-top: 20px; display: flex; flex-direction: column; gap: 20px; align-items: center;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('itemList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">🖨️ Print Item List</button>
      <button onclick="exportPDF('itemList', 'item_list_report.pdf')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px;">💾 Save Item List</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('carList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">🖨️ Print Car List</button>
      <button onclick="exportPDF('carList', 'car_list_report.pdf')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px;">💾 Save Car List</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('borrowedItemList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">🖨️ Print Borrowed Items</button>
      <button onclick="exportPDF('borrowedItemList', 'borrowed_items_report.pdf')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px;">💾 Save Borrowed Items</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('borrowedCarList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px;">🖨️ Print Borrowed Cars</button>
      <button onclick="exportPDF('borrowedCarList', 'borrowed_cars_report.pdf')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px;">💾 Save Borrowed Cars</button>
    </div>

    <!-- NEW BUTTONS -->
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-top: 20px;">
      <button onclick="printAllReports()" style="padding: 12px 24px; background-color: #e67e22; color: white; border: none; border-radius: 5px;">🖨️ Print All Reports</button>
      <button onclick="exportAllPDF('all_reports.pdf')" style="padding: 12px 24px; background-color: #9b59b6; color: white; border: none; border-radius: 5px;">💾 Download All Reports</button>
    </div>
  </div>

  <!-- Hidden Reports -->
  <div id="itemList" style="display:none;">
    <h3>Item List Report</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#27ae60; color:white;">
          <th>Code</th><th>Name</th><th>Category</th><th>Quantity</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td>{{ $item->code }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ $item->category }}</td>
          <td>{{ $item->quantity }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="carList" style="display:none;">
    <h3>Car List Report</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#2980b9; color:white;">
          <th>Code</th><th>Make/Model</th><th>Year</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cars as $car)
        <tr>
          <td>{{ $car->code }}</td>
          <td>{{ $car->make_model }}</td>
          <td>{{ $car->year }}</td>
          <td>{{ $car->status }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="borrowedItemList" style="display:none;">
    <h3>Borrowed Items Report</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#f39c12; color:white;">
          <th>Item</th><th>Borrower</th><th>Quantity</th><th>Borrow Date</th><th>Due Date</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($borrowedItems as $b)
        <tr>
          <td>{{ $b->item->name ?? 'N/A' }}</td>
          <td>{{ $b->borrower_name }}</td>
          <td>{{ $b->quantity_borrowed }}</td>
          <td>{{ \Carbon\Carbon::parse($b->borrow_date)->format('M d, Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($b->due_date)->format('M d, Y') }}</td>
          <td>{{ ucfirst($b->status) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="borrowedCarList" style="display:none;">
    <h3>Borrowed Cars Report</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:#e74c3c; color:white;">
          <th>Vehicle</th><th>Borrower</th><th>Borrow Date</th><th>Due Date</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($borrowedCars as $b)
        <tr>
          <td>{{ $b->car->make_model ?? 'N/A' }}</td>
          <td>{{ $b->borrower_name }}</td>
          <td>{{ \Carbon\Carbon::parse($b->borrow_date)->format('M d, Y') }}</td>
          <td>{{ \Carbon\Carbon::parse($b->due_date)->format('M d, Y') }}</td>
          <td>{{ ucfirst($b->status) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// Print single
function printReport(id) {
  const content = document.getElementById(id);
  if (!content) return;
  const win = window.open('', '_blank');
  win.document.write(`
    <html><head><title>${id}</title></head><body>
    <h1>Barangay Inventory System</h1>
    <h3>Report Generated: ${new Date().toLocaleDateString()}</h3>
    ${content.innerHTML}
    </body></html>
  `);
  win.document.close();
  win.print();
}

// Save single with rename
async function exportPDF(id, defaultFilename) {
  const { jsPDF } = window.jspdf;
  const content = document.getElementById(id);
  if (!content) return;

  let filename = prompt("Enter file name:", defaultFilename);
  if (!filename) return;
  if (!filename.endsWith(".pdf")) filename += ".pdf";

  const clone = content.cloneNode(true);
  clone.style.display = "block";
  document.body.appendChild(clone);

  const canvas = await html2canvas(clone, { scale: 2 });
  const imgData = canvas.toDataURL("image/png");
  const pdf = new jsPDF("p", "mm", "a4");
  const width = pdf.internal.pageSize.getWidth();
  const height = (canvas.height * width) / canvas.width;
  pdf.addImage(imgData, "PNG", 0, 0, width, height);
  pdf.save(filename);
  document.body.removeChild(clone);
}

// Print all
function printAllReports() {
  const ids = ["itemList", "carList", "borrowedItemList", "borrowedCarList"];
  const win = window.open('', '_blank');
  win.document.write(`<html><head><title>All Reports</title></head><body><h1>Barangay Inventory System</h1>`);
  ids.forEach(id => {
    const content = document.getElementById(id);
    if (content) win.document.write(`<h2>${content.querySelector('h3').textContent}</h2>${content.querySelector('table').outerHTML}<br><br>`);
  });
  win.document.write(`</body></html>`);
  win.document.close();
  win.print();
}

// Download all
async function exportAllPDF(defaultFilename) {
  const { jsPDF } = window.jspdf;
  const ids = ["itemList", "carList", "borrowedItemList", "borrowedCarList"];
  const pdf = new jsPDF("p", "mm", "a4");
  let yOffset = 10;

  for (let i = 0; i < ids.length; i++) {
    const content = document.getElementById(ids[i]);
    if (!content) continue;

    const clone = content.cloneNode(true);
    clone.style.display = "block";
    document.body.appendChild(clone);

    const canvas = await html2canvas(clone, { scale: 2 });
    const imgData = canvas.toDataURL("image/png");
    const width = pdf.internal.pageSize.getWidth();
    const height = (canvas.height * width) / canvas.width;

    if (i > 0) pdf.addPage();
    pdf.addImage(imgData, "PNG", 0, 0, width, height);
    document.body.removeChild(clone);
  }

  let filename = prompt("Enter file name for all reports:", defaultFilename);
  if (!filename) return;
  if (!filename.endsWith(".pdf")) filename += ".pdf";
  pdf.save(filename);
}
</script>
@endsection
