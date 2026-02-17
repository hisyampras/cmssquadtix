<!doctype html>
<html lang="id" class="h-full perf-lite">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Dashboard Portal</title>
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

    .perf-lite [class*="animate-"] { animation: none !important; }
    .perf-lite [class*="transition"] { transition: none !important; }
    .perf-lite [class*="bg-gradient"] { background-image: none !important; }
    .perf-lite [class*="backdrop-blur"] {
      backdrop-filter: none !important;
      -webkit-backdrop-filter: none !important;
    }
    .perf-lite [class*="blur-"] { filter: none !important; }
    .perf-lite [class*="shadow"] { box-shadow: none !important; }
  </style>
</head>

<body class="h-full bg-light text-light-50">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md animate-fade-up animation-delay-4000">

      <div class="mb-5 text-center">
        <h2 class="text-lg md:text-xl font-semibold text-light-50 tracking-tight">
          Sign in to PORTAL
        </h2>
        <p class="mt-1 text-xs text-light-400">
          Please use your internal account to continue.
        </p>
      </div>

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
          </div>

          <button type="submit"
                  class="w-full mt-1 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-500 active:scale-[0.98] transition">
            <span>Sign in</span>
          </button>

        </form>

        <p class="mt-4 text-[10px] text-light-500 text-center">
          If you encounter any access issues, please contact IT or Admin team.
        </p>

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
