@extends('layouts.inventory')



@section('content')
<section>
  <h2 style="text-align: center; color: #2c3e50;">Reports</h2>
  
  <!-- Report Summary Boxes -->
  <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin: 20px 0;">
    <div style="flex: 1 1 200px; max-width: 150px; background: #27ae60; color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h3 style="margin: 0; font-size: 28px;">{{ $items->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Total Items</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #2980b9; color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h3 style="margin: 0; font-size: 28px;">{{ $cars->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Total Cars</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #f39c12; color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h3 style="margin: 0; font-size: 28px;">{{ $borrowedItems->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Borrowed Items</p>
    </div>
    <div style="flex: 1 1 200px; max-width: 150px; background: #15c8ff; color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h3 style="margin: 0; font-size: 28px;">{{ $borrowedCars->count() }}</h3>
      <p style="margin: 5px 0 0 0; font-weight: bold;">Borrowed Cars</p>
    </div>
  </div>
  </div>

  <!-- Report Actions -->
  <div class="report-actions" style="margin-top: 20px; display: flex; flex-direction: column; gap: 20px; align-items: center;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('itemList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">🖨️ Print Item List Report</button>
      <button onclick="exportCSV('itemList', 'item_list_report.csv')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">💾 Save Item List Report</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('carList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">🖨️ Print Car List Report</button>
      <button onclick="exportCSV('carList', 'car_list_report.csv')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">💾 Save Car List Report</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('borrowedItemList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">🖨️ Print Borrowed Items Report</button>
      <button onclick="exportCSV('borrowedItemList', 'borrowed_items_report.csv')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">💾 Save Borrowed Items Report</button>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
      <button onclick="printReport('borrowedCarList')" style="padding: 12px 24px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">🖨️ Print Borrowed Cars Report</button>
      <button onclick="exportCSV('borrowedCarList', 'borrowed_cars_report.csv')" style="padding: 12px 24px; background-color: #2980b9; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">💾 Save Borrowed Cars Report</button>
    </div>
  </div>

  <!-- Hidden Report Data -->
  <div id="itemList" style="display: none;">
    <h3>Item List Report</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
      <thead>
        <tr style="background: #27ae60; color: white;">
          <th style="border: 1px solid #ddd; padding: 8px;">Code</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Name</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Category</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Quantity</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->code }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->category }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->quantity }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="carList" style="display: none;">
    <h3>Car List Report</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
      <thead>
        <tr style="background: #2980b9; color: white;">
          <th style="border: 1px solid #ddd; padding: 8px;">Code</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Make/Model</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Year</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cars as $car)
        <tr>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $car->code }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $car->make_model }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $car->year }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $car->status }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="borrowedItemList" style="display: none;">
    <h3>Borrowed Items Report</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
      <thead>
        <tr style="background: #f39c12; color: white;">
          <th style="border: 1px solid #ddd; padding: 8px;">Item</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Borrower</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Quantity</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Borrow Date</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Due Date</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($borrowedItems as $borrowed)
        <tr>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $borrowed->item->name ?? 'N/A' }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $borrowed->borrower_name }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $borrowed->quantity_borrowed }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($borrowed->borrow_date)->format('M d, Y') }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($borrowed->due_date)->format('M d, Y') }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($borrowed->status) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div id="borrowedCarList" style="display: none;">
    <h3>Borrowed Cars Report</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
      <thead>
        <tr style="background: #e74c3c; color: white;">
          <th style="border: 1px solid #ddd; padding: 8px;">Vehicle</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Borrower</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Borrow Date</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Due Date</th>
          <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($borrowedCars as $borrowed)
        <tr>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $borrowed->car->make_model ?? 'N/A' }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ $borrowed->borrower_name }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($borrowed->borrow_date)->format('M d, Y') }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ \Carbon\Carbon::parse($borrowed->due_date)->format('M d, Y') }}</td>
          <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($borrowed->status) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection
@section('scripts')
function printReport(id) {
    const reportContent = document.getElementById(id);
    if (reportContent) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>${id} Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        h1, h3 { color: #333; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <h1>Barangay Inventory System</h1>
                    <h3>Report Generated: ${new Date().toLocaleDateString()}</h3>
                    ${reportContent.innerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

function exportCSV(id, filename) {
    const reportContent = document.getElementById(id);
    if (reportContent) {
        const table = reportContent.querySelector('table');
        if (table) {
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cols = row.querySelectorAll('th, td');
                const rowData = Array.from(cols).map(col => `"${col.textContent.trim()}"`);
                csv.push(rowData.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    }
}
@endsection


