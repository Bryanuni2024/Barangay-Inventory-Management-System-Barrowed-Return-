<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Barangay Inventory System</title>
  <link rel="icon" type="image/png" href="{{ asset('inventory/assets/logo.jpg') }}">
  <link rel="stylesheet" href="{{ asset('inventory/assets/style.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <style>
    nav {
  display: flex;
  justify-content: center; /* centers the buttons horizontally */
  gap: 50px; /* space between buttons */
  padding: 5px 0; /* optional padding */
  background-color: #27ae60; /* optional nav background */
}

nav button {
  background-color: white;
  color: #27ae60;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  transition: transform 0.2s, background-color 0.2s;
}

nav button:hover {
  background-color: #2ecc71;
  color: white;
  transform: translateY(-2px);
}


    .alert {
      padding: 15px;
      margin: 10px 0;
      border-radius: 5px;
      font-weight: bold;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .alert ul {
      margin: 0;
      padding-left: 20px;
    }
    /* Modal background */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1000; 
      left: 0; 
      top: 0;
      width: 100%; 
      height: 100%; 
      background-color: rgba(0,0,0,0.5); /* dim background */
    }
    
    /* Modal when shown */
    .modal.show {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }

    /* Modal box */
    .modal-content {
      background: #fff !important;
      padding: 20px !important;
      border-radius: 8px !important;
      width: 440px !important;
      max-width: 90% !important;
      position: relative !important;
      margin: 0 !important;
      box-shadow: none !important;
      border: none !important;
    }
    /* Close button */
    .close {
      position: absolute;
      right: 10px;
      top: 10px;
      font-size: 22px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      color: white;
      margin: 5px;
    }
    .btn-borrow { background-color: #2e86c1; }
    .btn-edit { background-color: #4CAF50; }
    .btn-delete { background-color: #e74c3c; }
    .btn-return { background-color: #27ae60; }
    .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    .overdue { color: #e74c3c; font-weight: bold; }
    .returned { color: #27ae60; font-weight: bold; }
    .active { color: #2e86c1; font-weight: bold; }
    
    /* Table styles */
    table { 
      width: 100%; 
      border-collapse: collapse; 
      margin-top: 15px; 
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    th, td { 
      border: 1px solid #ddd; 
      padding: 12px; 
      text-align: center; 
    }
    th { 
      background-color: #27ae60; 
      color: white; 
      font-weight: bold;
    }
    tr:nth-child(even) { 
      background-color: #f8f9fa; 
    }
    tr:hover { 
      background-color: #e8f5e8; 
    }
    
    /* Form styles */
    .table-actions { 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
      margin: 15px 0; 
    }
    .search-input { 
      padding: 8px 12px; 
      border: 1px solid #ccc; 
      border-radius: 5px; 
      font-size: 15px; 
      width: 250px; 
    }
    
    /* Section styles */
    section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin: 20px 0;
    }
    
    h2 {
      color: #27ae60;
      margin-bottom: 20px;
      border-bottom: 2px solid #27ae60;
      padding-bottom: 10px;
    }
    
    /* Modal form styles */
    .modal h3 {
      color: #27ae60;
      margin-bottom: 20px;
      text-align: center;
    }
    
    .modal label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    
    .modal input, .modal textarea {
      border: 1px solid #ddd;
      border-radius: 4px;
      transition: border-color 0.3s ease;
    }
    
    .modal input:focus, .modal textarea:focus {
      outline: none;
      border-color: #27ae60;
      box-shadow: 0 0 5px rgba(39, 174, 96, 0.3);
    }
    
    .modal small {
      display: block;
      margin-top: 5px;
      font-size: 12px;
    }
    
    /* Prevent body scroll when modal is open */
    body.modal-open {
      overflow: hidden;
    }
@yield('styles')
  </style>
</head>
<body>




<header style="background: #222d32; box-shadow: 0 2px 8px rgba(0,0,0,0.10); padding: 0; margin-bottom: 0; border-bottom: 2px solid #27ae60;">
  <div style="display: flex; align-items: center; max-width: 1400px; margin: 0 auto; height: 56px;">
    <!-- Left: Logo -->
    <div style="flex: 0 0 auto; display: flex; align-items: center; height: 100%; padding-left: 18px; padding-right: 325px">
      <img src='{{ asset('inventory/assets/POBLACION.png') }}' alt='Logo' style='height: 36px; border-radius: 6px; background: #fff; padding: 5px; border: 1.5px solid #27ae60; box-shadow: 0 1px 2px rgba(0,0,0,0.04);'>
    </div>
    <!-- Center: System Name -->
    <div style="flex: 1 1 0%; display: flex; align-items: center; justify-content: center;">
      <span style="font-size: 1.45rem; font-weight: 700; color: #e9ffe9; letter-spacing: 1.5px;padding-left: 18px; text-shadow: 0 2px 4px rgba(0,0,0,0.10);">Barangay Inventory Management System</span>
    </div>
    <!-- Right: Logout -->
    <div style="flex: 0 0 auto; display: flex; align-items: center; gap: 100px; height: 100%; padding-left: 325px; padding-right: 18px;">
      <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="logout-btn" style="background: linear-gradient(90deg, #e74c3c 60%, #c0392b 100%); color: #fff; border: none; padding: 5px 40px 5px 32px; border-radius: 16px; cursor: pointer; font-weight: bold; position: relative; font-size: 1rem; box-shadow: 0 1px 4px rgba(231,76,60,0.10); transition: background 0.2s;">
          <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 1.1em;">&#x2716;</span>
          Logout
        </button>
      </form>
    </div>
  </div>
</header>

<nav style="display: flex; justify-content: center; gap: 50px; padding: 10px 0; background-color: #27ae60; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-top: 0;">
  <button onclick="location.href='{{ route('inventory.dashboard') }}'">Dashboard</button>
  <button onclick="location.href='{{ route('inventory.items') }}'">Item List</button>
  <button onclick="location.href='{{ route('inventory.cars') }}'">Car List</button>
  <button onclick="location.href='{{ route('inventory.borrowed_items') }}'">Borrowed Items</button>
  <button onclick="location.href='{{ route('inventory.borrowed_cars') }}'">Borrowed Cars</button>
  <button onclick="location.href='{{ route('inventory.reports') }}'">Reports</button>
</nav>

<main class="main-content">
  @if(session('status'))
    <div class="alert alert-success">
      {{ session('status') }}
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">
      {{ session('error') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-error">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @yield('content')
</main>

<!-- Include modals -->
@yield('modals')

<script>
// Prevent back button access after logout
window.addEventListener('pageshow', function(event) {
  // Check if the page was loaded from cache (back button)
  if (event.persisted) {
    // Force reload to check authentication
    window.location.reload();
  }
});

// Prevent caching of authenticated pages
window.addEventListener('beforeunload', function() {
  // Clear any cached data
  if (window.performance && window.performance.navigation.type === 1) {
    // Page was refreshed, check authentication
    fetch('{{ route("login") }}', {
      method: 'HEAD',
      cache: 'no-cache'
    }).then(response => {
      if (response.redirected) {
        window.location.href = '{{ route("login") }}';
      }
    });
  }
});

// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
  // Close modal when clicking the X
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('close')) {
      const modal = e.target.closest('.modal');
      if (modal) {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
      }
    }
  });

  // Close modal when clicking outside
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      e.target.classList.remove('show');
      document.body.classList.remove('modal-open');
    }
  });
  
  // Add body class when modal opens
  document.addEventListener('click', function(e) {
    if (e.target.onclick && e.target.onclick.toString().includes('getElementById') && e.target.onclick.toString().includes('classList.add')) {
      document.body.classList.add('modal-open');
    }
  });
});

@yield('scripts')
</script>
</body>
</html>


