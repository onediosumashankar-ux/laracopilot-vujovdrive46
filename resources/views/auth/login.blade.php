<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | TalentFlow HRMS</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <style>
    body { background: linear-gradient(135deg, #0f1e35 0%, #1a3a5c 50%, #2d6a9f 100%); min-height: 100vh; }
    .login-card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); border: none; overflow: hidden; }
    .login-header { background: linear-gradient(135deg, #1a2942 0%, #2d6a9f 100%); padding: 2.5rem; text-align: center; }
    .login-body { padding: 2rem 2.5rem 2.5rem; }
    .form-control { border-radius: 8px; border: 1px solid #dee2e6; padding: 0.75rem 1rem; font-size: 0.95rem; }
    .form-control:focus { border-color: #2d6a9f; box-shadow: 0 0 0 3px rgba(45,106,159,0.15); }
    .btn-login { background: linear-gradient(135deg, #1a3a5c, #2d6a9f); border: none; border-radius: 8px; padding: 0.875rem; font-size: 1rem; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s; }
    .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(45,106,159,0.4); }
    .cred-card { background: #f8f9fa; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 0.5rem; border-left: 4px solid #2d6a9f; }
    .cred-card.hr { border-left-color: #28a745; }
    .cred-card.emp { border-left-color: #fd7e14; }
    .input-group-text { background: #f8f9fa; border-right: none; border-radius: 8px 0 0 8px; }
    .form-control { border-left: none; border-radius: 0 8px 8px 0; }
    .input-group .form-control:not(:last-child) { border-right: 1px solid #dee2e6; }
  </style>
</head>
<body class="d-flex align-items-center">
<div class="container py-5">
  <div class="row justify-content-center align-items-start">
    <div class="col-md-5">
      <div class="login-card card">
        <div class="login-header">
          <i class="fas fa-users-cog fa-3x mb-3" style="color:#4db8ff"></i>
          <h2 class="text-white font-weight-bold mb-1">TalentFlow HRMS</h2>
          <p class="text-light mb-0" style="opacity:0.8">Human Resource Management System</p>
        </div>
        <div class="login-body">
          <h5 class="font-weight-bold mb-4" style="color:#1a2942"><i class="fas fa-sign-in-alt mr-2 text-primary"></i>Sign In to Your Account</h5>
          @if($errors->any())
          <div class="alert alert-danger border-0" style="border-radius:8px">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
          </div>
          @endif
          @if(session('success'))
          <div class="alert alert-success border-0" style="border-radius:8px">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
          </div>
          @endif
          <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
              <label class="font-weight-600 small" style="color:#495057">Email Address</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                </div>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
              </div>
            </div>
            <div class="form-group mb-4">
              <label class="font-weight-600 small" style="color:#495057">Password</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                </div>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
              </div>
            </div>
            <button type="submit" class="btn btn-login btn-primary btn-block text-white">
              <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-5 mt-4 mt-md-0">
      <div class="card" style="border-radius:16px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.3)">
        <div class="card-header" style="background:#1a2942;border-radius:16px 16px 0 0;border:none">
          <h6 class="mb-0 text-white"><i class="fas fa-key mr-2 text-warning"></i>Demo Login Credentials</h6>
        </div>
        <div class="card-body p-3">
          <div class="cred-card">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="badge badge-primary"><i class="fas fa-crown mr-1"></i>Super Admin</span>
            </div>
            <div class="small"><strong>Email:</strong> superadmin@hrms.com</div>
            <div class="small"><strong>Password:</strong> password123</div>
          </div>
          <div class="cred-card">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="badge badge-info"><i class="fas fa-shield-alt mr-1"></i>Admin (TechCorp)</span>
            </div>
            <div class="small"><strong>Email:</strong> admin@techcorp.com</div>
            <div class="small"><strong>Password:</strong> password123</div>
          </div>
          <div class="cred-card hr">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="badge badge-success"><i class="fas fa-user-tie mr-1"></i>HR Manager</span>
            </div>
            <div class="small"><strong>Email:</strong> hr@techcorp.com</div>
            <div class="small"><strong>Password:</strong> password123</div>
          </div>
          <div class="cred-card emp">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="badge badge-warning"><i class="fas fa-user mr-1"></i>Employee</span>
            </div>
            <div class="small"><strong>Email:</strong> james.wilson@techcorp.com</div>
            <div class="small"><strong>Password:</strong> password123</div>
          </div>
          <div class="text-muted small mt-2 text-center">
            <i class="fas fa-info-circle mr-1"></i>3 tenants available: TechCorp, GlobalFinance, HealthFirst
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
