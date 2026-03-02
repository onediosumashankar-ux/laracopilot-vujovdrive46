<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'HRMS CRM') | TalentFlow HRMS</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <style>
    .brand-link { background: linear-gradient(135deg, #1a3a5c 0%, #2d6a9f 100%); }
    .main-sidebar { background: #1a2942; }
    .nav-sidebar .nav-item .nav-link { color: #a8b5c8; }
    .nav-sidebar .nav-item .nav-link:hover, .nav-sidebar .nav-item .nav-link.active { color: #fff; background: rgba(45,106,159,0.4); }
    .nav-sidebar .nav-item .nav-link i { color: #6b8caa; }
    .nav-sidebar .nav-item .nav-link.active i, .nav-sidebar .nav-item .nav-link:hover i { color: #4db8ff; }
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active { background: linear-gradient(90deg, #2d6a9f, #1a3a5c); color: #fff; }
    .content-wrapper { background: #f0f4f8; }
    .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .card-header { border-radius: 10px 10px 0 0 !important; }
    .info-box { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
    .nav-pills .nav-link.active { background: linear-gradient(135deg, #1a3a5c, #2d6a9f); }
    .badge { border-radius: 6px; }
    .btn { border-radius: 6px; }
    .main-header { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .nav-header { color: #6b8caa; font-size: 0.7rem; letter-spacing: 1px; padding: 0.5rem 1rem; }
  </style>
  @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link text-muted"><i class="fas fa-building mr-1"></i>{{ session('hrms_tenant_name', 'TalentFlow HRMS') }}</span>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><span class="nav-link"><i class="far fa-clock mr-1"></i><span id="clock"></span></span></li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <div class="d-inline-block" style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#2d6a9f,#1a3a5c);display:inline-flex!important;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600">{{ strtoupper(substr(session('hrms_user_name', 'U'), 0, 1)) }}</div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-header"><strong>{{ session('hrms_user_name') }}</strong><br><small class="text-muted">{{ ucfirst(session('hrms_role')) }}</small></div>
          <div class="dropdown-divider"></div>
          @if(session('hrms_role') === 'employee')
          <a href="{{ route('employee.profile') }}" class="dropdown-item"><i class="fas fa-user mr-2"></i>My Profile</a>
          @endif
          <div class="dropdown-divider"></div>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i>Logout</button>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center py-3">
      <i class="fas fa-users-cog fa-lg mr-2" style="color:#4db8ff"></i>
      <span class="brand-text font-weight-bold" style="color:#fff;font-size:1.1rem">TalentFlow</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center" style="border-bottom:1px solid rgba(255,255,255,0.1)">
        <div class="image" style="width:35px;height:35px;border-radius:50%;background:linear-gradient(135deg,#2d6a9f,#4db8ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:14px;flex-shrink:0">{{ strtoupper(substr(session('hrms_user_name', 'U'), 0, 1)) }}</div>
        <div class="info ml-2">
          <a href="#" class="d-block" style="color:#fff;font-size:0.85rem;font-weight:600">{{ session('hrms_user_name') }}</a>
          <small style="color:#6b8caa">{{ ucfirst(session('hrms_role')) }}</small>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          @if(in_array(session('hrms_role'), ['superadmin']))
          <li class="nav-header">SUPER ADMIN</li>
          <li class="nav-item"><a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="{{ route('superadmin.tenants.index') }}" class="nav-link {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}"><i class="nav-icon fas fa-building"></i><p>Tenants</p></a></li>
          @endif
          @if(in_array(session('hrms_role'), ['admin', 'hr']))
          <li class="nav-header">MAIN MENU</li>
          <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"><i class="nav-icon fas fa-users"></i><p>Employees</p></a></li>
          <li class="nav-header">RECRUITMENT</li>
          <li class="nav-item"><a href="{{ route('admin.recruitment.index') }}" class="nav-link {{ request()->routeIs('admin.recruitment.*') ? 'active' : '' }}"><i class="nav-icon fas fa-briefcase"></i><p>Jobs & ATS</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.onboarding.index') }}" class="nav-link {{ request()->routeIs('admin.onboarding.*') ? 'active' : '' }}"><i class="nav-icon fas fa-user-plus"></i><p>Onboarding</p></a></li>
          <li class="nav-header">WORKFORCE</li>
          <li class="nav-item"><a href="{{ route('admin.attendance.index') }}" class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"><i class="nav-icon fas fa-clock"></i><p>Attendance</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.leaves.index') }}" class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}"><i class="nav-icon fas fa-calendar-minus"></i><p>Leave Management</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.performance.index') }}" class="nav-link {{ request()->routeIs('admin.performance.*') ? 'active' : '' }}"><i class="nav-icon fas fa-chart-line"></i><p>Performance</p></a></li>
          <li class="nav-header">COMPENSATION</li>
          <li class="nav-item"><a href="{{ route('admin.payroll.index') }}" class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}"><i class="nav-icon fas fa-money-bill-wave"></i><p>Payroll</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.tds.index') }}" class="nav-link {{ request()->routeIs('admin.tds.*') ? 'active' : '' }}"><i class="nav-icon fas fa-file-invoice"></i><p>TDS Management</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.benefits.index') }}" class="nav-link {{ request()->routeIs('admin.benefits.*') ? 'active' : '' }}"><i class="nav-icon fas fa-heartbeat"></i><p>Benefits</p></a></li>
          <li class="nav-header">DEVELOPMENT</li>
          <li class="nav-item"><a href="{{ route('admin.learning.index') }}" class="nav-link {{ request()->routeIs('admin.learning.*') ? 'active' : '' }}"><i class="nav-icon fas fa-graduation-cap"></i><p>Learning & Dev</p></a></li>
          <li class="nav-item"><a href="{{ route('admin.wellness.index') }}" class="nav-link {{ request()->routeIs('admin.wellness.*') ? 'active' : '' }}"><i class="nav-icon fas fa-smile"></i><p>Wellness</p></a></li>
          <li class="nav-header">INSIGHTS</li>
          <li class="nav-item"><a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}"><i class="nav-icon fas fa-chart-bar"></i><p>HR Analytics</p></a></li>
          @endif
          @if(session('hrms_role') === 'employee')
          <li class="nav-header">MY WORKSPACE</li>
          <li class="nav-item"><a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}"><i class="nav-icon fas fa-home"></i><p>My Dashboard</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.profile') }}" class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}"><i class="nav-icon fas fa-user"></i><p>My Profile</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.attendance.index') }}" class="nav-link {{ request()->routeIs('employee.attendance.*') ? 'active' : '' }}"><i class="nav-icon fas fa-clock"></i><p>Attendance</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.leaves.index') }}" class="nav-link {{ request()->routeIs('employee.leaves.*') ? 'active' : '' }}"><i class="nav-icon fas fa-calendar-check"></i><p>My Leaves</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.payslips') }}" class="nav-link {{ request()->routeIs('employee.payslips*') ? 'active' : '' }}"><i class="nav-icon fas fa-file-invoice-dollar"></i><p>Payslips</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.learning') }}" class="nav-link {{ request()->routeIs('employee.learning*') ? 'active' : '' }}"><i class="nav-icon fas fa-book"></i><p>Learning</p></a></li>
          <li class="nav-item"><a href="{{ route('employee.wellness') }}" class="nav-link {{ request()->routeIs('employee.wellness*') ? 'active' : '' }}"><i class="nav-icon fas fa-heart"></i><p>Wellness</p></a></li>
          @endif
        </ul>
      </nav>
    </div>
  </aside>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0" style="font-size:1.4rem;font-weight:700;color:#1a2942">@yield('page-title', 'Dashboard')</h1></div>
          <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li></ol></div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle mr-2"></i>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
        @endif
        @yield('content')
      </div>
    </div>
  </div>
  <footer class="main-footer">
    <strong>&copy; {{ date('Y') }} <a href="#">TalentFlow HRMS</a>.</strong> All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0 &nbsp;|&nbsp; Made with <i class="fas fa-heart text-danger"></i> by <a href="https://laracopilot.com/" target="_blank">LaraCopilot</a>
    </div>
  </footer>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script>
function updateClock(){const n=new Date();document.getElementById('clock').textContent=n.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});}
updateClock();setInterval(updateClock,1000);
</script>
@stack('scripts')
</body>
</html>
