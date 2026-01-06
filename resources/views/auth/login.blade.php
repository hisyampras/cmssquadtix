<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • ASM – PORTAL</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-staco.png') }}">

  <!-- Inter + Tailwind CDN -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui']
          },
          colors: {
            brand: {
              blue: '#004AAD',
              cyan: '#00B4D8',
              dark: '#020617'
            }
          },
          boxShadow: {
            'soft-xl': '0 24px 60px rgba(15,23,42,0.18)'
          },
          keyframes: {
            'blob-float': {
              '0%, 100%': { transform: 'translateY(0px) scale(1)' },
              '50%': { transform: 'translateY(-18px) scale(1.03)' },
            },
            'fade-up': {
              '0%': { opacity: '0', transform: 'translateY(12px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            'btn-pulse': {
              '0%': { boxShadow: '0 0 0 0 rgba(0,74,173,0.45)', transform: 'translateY(0)' },
              '70%': { boxShadow: '0 0 0 12px rgba(0,74,173,0)', transform: 'translateY(-1px)' },
              '100%': { boxShadow: '0 0 0 0 rgba(0,74,173,0)', transform: 'translateY(0)' },
            },
          },
          animation: {
            'blob-float-slow': 'blob-float 18s ease-in-out infinite',
            'blob-float-fast': 'blob-float 14s ease-in-out infinite',
            'fade-up': 'fade-up 0.7s ease-out forwards',
            'fade-up-slow': 'fade-up 0.9s ease-out forwards',
            'btn-pulse': 'btn-pulse 2.8s ease-out infinite',
          }
        }
      }
    };
  </script>

  <style>
    .animation-delay-2000 {
      animation-delay: 2s;
    }
    .animation-delay-4000 {
      animation-delay: 4s;
    }
  </style>
</head>

<body class="h-full bg-light text-light-50">
  <div class="min-h-screen flex items-center justify-center px-4">

    <!-- Decorative background -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
      <div class="absolute -top-32 -left-16 h-64 w-64 rounded-full bg-brand-blue/30 blur-3xl animate-blob-float-slow"></div>
      <div class="absolute -bottom-40 -right-10 h-72 w-72 rounded-full bg-brand-cyan/25 blur-3xl animate-blob-float-fast animation-delay-2000"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(248,250,252,0.05),_transparent_50%),radial-gradient(circle_at_bottom,_rgba(148,163,184,0.09),_transparent_55%)]"></div>
    </div>

    <!-- WRAPPER (Diperkecil max-width) -->
    <div class="relative w-full max-w-3xl animate-fade-up-slow">
      <div class="grid gap-6 md:grid-cols-2 items-stretch">

        <!-- LEFT PANEL (Diperkecil padding & radius) -->
        <div class="hidden md:flex flex-col justify-between rounded-2xl border border-light-800/70 bg-light-900/60 backdrop-blur-xl p-6 shadow-soft-xl animate-fade-up animation-delay-2000">

          <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-light-700 bg-light-900/70 px-3 py-1 text-[11px] font-medium text-light-300">
              <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400 mr-1"></span>
              ASM – Internal Platform
            </div>

            <div class="mt-5 space-y-3">
              <h1 class="text-2xl font-semibold tracking-tight text-light-50">
                Welcome to <br>ASM – PORTAL
              </h1>
              <p class="text-xs text-light-300 leading-relaxed">
                A centralized platform for production monitoring, claim insights, and internal reporting at PT Asuransi Staco Mandiri.
              </p>
            </div>
          </div>

          <div class="mt-6 space-y-3 text-xs text-light-400">
            <div class="flex items-center gap-3">
              <div class="flex h-7 w-7 items-center justify-center rounded-xl bg-light-800/80 border border-light-700">
                🔒
              </div>
              <div>
                <p class="font-medium text-light-200 text-[12px]">Secure Access</p>
                <p>Role-based authentication</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="flex h-7 w-7 items-center justify-center rounded-xl bg-light-800/80 border border-light-700">
                📊
              </div>
              <div>
                <p class="font-medium text-light-200 text-[12px]">Real-time Dashboard</p>
                <p>All insights in one view.</p>
              </div>
            </div>
          </div>

          <p class="pt-4 border-t border-light-800 text-[10px] text-light-500">
            © {{ date('Y') }} PT Asuransi Staco Mandiri — Internal use only.
          </p>

        </div>

        <!-- RIGHT PANEL (Login form) -->
        <div class="flex items-center">
          <div class="w-full animate-fade-up animation-delay-4000">

            <div class="mb-5 text-center md:text-left">
              <img src="{{ asset('images/logo-staco.png') }}" alt="Logo" class="h-10 object-contain mb-2 mx-auto md:mx-0">
              <h2 class="text-lg md:text-xl font-semibold text-light-50 tracking-tight">
                Sign in to ASM – PORTAL
              </h2>
              <p class="mt-1 text-xs text-light-400">
                Please use your ASM internal account to continue.
              </p>
            </div>

            <!-- Form Box (Diperkecil p-5 / p-6) -->
            <div class="rounded-2xl border border-light-800/80 bg-light-900/80 backdrop-blur-xl shadow-soft-xl p-5 md:p-6">

              @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-200">
                  {{ session('status') }}
                </div>
              @endif

              @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-500/50 bg-rose-500/10 px-3 py-2 text-xs text-rose-200">
                  {{ $errors->first() }}
                </div>
              @endif

              <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                  <label for="email" class="block text-xs font-medium text-light-200 mb-1">Email</label>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                         class="block w-full rounded-lg border border-light-700 bg-light-900/60 px-3 py-2.5 text-sm text-light-50 placeholder:text-light-500 focus:ring-brand-blue focus:border-brand-blue outline-none" />
                </div>

                <div>
                  <label for="password" class="block text-xs font-medium text-light-200 mb-1">Password</label>
                  <div class="relative">
                    <input id="password" name="password" type="password" required
                           class="block w-full rounded-lg border border-light-700 bg-light-900/60 px-3 py-2.5 text-sm text-light-50 placeholder:text-light-500 focus:ring-brand-blue focus:border-brand-blue outline-none" />
                    <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-3 flex items-center text-light-500 text-xs">
                      Show
                    </button>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <label class="inline-flex items-center gap-2 text-xs text-light-300">
                    <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-light-600 bg-light-900 text-brand-blue">
                    Remember me
                  </label>

                  @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-cyan hover:text-brand-blue">Forgot password?</a>
                  @endif
                </div>

                <button type="submit"
                        class="w-full mt-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-blue to-brand-cyan px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-blue/40 hover:brightness-110 active:scale-[0.98] transition animate-btn-pulse">
                  <span>Sign in</span>
                </button>

              </form>

              <p class="mt-4 text-[10px] text-light-500 text-center md:text-left">
                If you encounter any access issues, please contact the ASM IT or Admin team.
              </p>

            </div>

          </div>
        </div>

      </div>
    </div>

  </div>

  <script>
    const btn = document.getElementById('togglePassword');
    const pwd = document.getElementById('password');
    btn?.addEventListener('click', () => {
      const hidden = pwd.type === 'password';
      pwd.type = hidden ? 'text' : 'password';
      btn.textContent = hidden ? 'Hide' : 'Show';
    });
  </script>

</body>
</html>
