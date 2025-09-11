@extends('layouts.inventory')

@section('styles')
.stats-container { 
  display: flex; 
  gap: 20px; 
  flex-wrap: wrap; 
  margin: 20px 0; 
}
.stat-box { 
  background: linear-gradient(135deg, #27ae60, #2ecc71); 
  color: white; 
  padding: 15px; 
  border-radius: 10px; 
  flex: 1 1 150px; 
  text-align: center; 
  font-size: 18px; 
  font-weight: bold; 
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}
.stat-box:hover {
  transform: translateY(-5px);
}
.chart-container {
  background: white;
  padding: 10px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin: 15px 0;
  width: 100%;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  overflow-x: auto;
  box-sizing: border-box;
  display: block;
}
canvas#dashboardChart {
  width: 100% !important;
  max-width: 600px !important;
  height: 300px !important;
  display: block;
  margin: 0 auto;
}
}
canvas#dashboardChart {
  width: 100% !important;
  max-width: 100vw !important;
  height: 350px !important;
  display: block;
}
}
@endsection

@section('content')
  <section>
    <h2>Dashboard</h2>
    <div class="stats-container">
      <div class="stat-box">Total Items: {{ $totalItems }}</div>
      <div class="stat-box">Total Cars: {{ $totalCars }}</div>
      <div class="stat-box">Borrowed Items: {{ $borrowedItems }}</div>
      <div class="stat-box">Borrowed Cars: {{ $borrowedCars }}</div>
      <div class="stat-box">Overdue Items: {{ $overdueItems }}</div>
      <div class="stat-box">Overdue Cars: {{ $overdueCars }}</div>
    </div>
    
    <div class="chart-container">
      <h3>Inventory Overview</h3>
      <canvas id="dashboardChart"></canvas>
    </div>
    
    <div class="recent-activities">
      <h3>Recent Activities</h3>
      <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
          <h4>Recent Borrowed Items</h4>
          @if($recentBorrowedItems->count() > 0)
            <table>
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Borrower</th>
                  <th>Quantity</th>
                  <th>Due Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentBorrowedItems as $borrowed)
                  <tr>
                    <td>{{ $borrowed->item->name ?? 'N/A' }}</td>
                    <td>{{ $borrowed->borrower_name }}</td>
                    <td>{{ $borrowed->quantity_borrowed }}</td>
                    <td>{{ \Carbon\Carbon::parse($borrowed->due_date)->format('M d, Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <p>No recent borrowed items.</p>
          @endif
        </div>
        
        <div style="flex: 1; min-width: 300px;">
          <h4>Recent Borrowed Cars</h4>
          @if($recentBorrowedCars->count() > 0)
            <table>
              <thead>
                <tr>
                  <th>Vehicle</th>
                  <th>Borrower</th>
                  <th>Due Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentBorrowedCars as $borrowed)
                  <tr>
                    <td>{{ $borrowed->car->make_model ?? 'N/A' }}</td>
                    <td>{{ $borrowed->borrower_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($borrowed->due_date)->format('M d, Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <p>No recent borrowed cars.</p>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
const ctx = document.getElementById('dashboardChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: { 
    labels: ['Total Items', 'Total Cars', 'Borrowed Items', 'Borrowed Cars', 'Overdue Items', 'Overdue Cars'], 
    datasets: [{ 
      label: 'Dashboard Stats', 
      data: [{{ $totalItems }}, {{ $totalCars }}, {{ $borrowedItems }}, {{ $borrowedCars }}, {{ $overdueItems }}, {{ $overdueCars }}], 
      backgroundColor: ['#4caf50','#2196f3','#ff9800','#9c27b0','#f44336','#e91e63'], 
      borderRadius: 5 
    }] 
  },
  options: { 
    responsive: true, 
    scales: { 
      y: { 
        beginAtZero: true, 
        ticks: { stepSize: 1 } 
      } 
    } 
  }
});
@endsection


