<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • Squadtix</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-squadtix.png') }}">

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
              cyan: '#00E5FF',
              dark: '#020617'
            }
          },
          boxShadow: {
            'soft-xl': '0 24px 60px rgba(15,23,42,0.18)'
          }
        }
      }
    };
  </script>
</head>

<body class="h-full bg-light text-light-50">
<div class="min-h-screen flex items-center justify-center px-4">

  <!-- Decorative background -->
  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="absolute -top-32 -left-16 h-64 w-64 rounded-full bg-brand-blue/30 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-10 h-72 w-72 rounded-full bg-brand-cyan/25 blur-3xl"></div>
  </div>

  <div class="relative w-full max-w-3xl">
    <div class="grid gap-6 md:grid-cols-2 items-stretch">

      <!-- LEFT PANEL -->
      <div class="hidden md:flex flex-col justify-between rounded-2xl border border-light-800/70 bg-light-900/60 backdrop-blur-xl p-6 shadow-soft-xl">

        <div>
          <div class="inline-flex items-center gap-2 rounded-full border border-light-700 bg-light-900/70 px-3 py-1 text-[11px] font-medium text-light-300">
            <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400 mr-1"></span>
            Event Gate System
          </div>

          <div class="mt-5 space-y-3">
            <h1 class="text-2xl font-semibold tracking-tight text-light-50">
              Welcome to <br>Squadtix
            </h1>
            <p class="text-xs text-light-300 leading-relaxed">
              Secure access platform for event gate operations, ticket scanning,
              and real-time validation — built for speed and accuracy.
            </p>
          </div>
        </div>

        <div class="mt-6 space-y-3 text-xs text-light-400">
          <div class="flex items-center gap-3">
            <div class="flex h-7 w-7 items-center justify-center rounded-xl bg-light-800/80 border border-light-700">🎟️</div>
            <div>
              <p class="font-medium text-light-200 text-[12px]">Ticket Validation</p>
              <p>Instant QR verification</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="flex h-7 w-7 items-center justify-center rounded-xl bg-light-800/80 border border-light-700">🚪</div>
            <div>
              <p class="font-medium text-light-200 text-[12px]">Gate Control</p>
              <p>Zone & access management</p>
            </div>
          </div>
        </div>

        <p class="pt-4 border-t border-light-800 text-[10px] text-light-500">
          © {{ date('Y') }} Squadtix — Event Access Technology
        </p>
      </div>

      <!-- RIGHT PANEL -->
      <div class="flex items-center">
        <div class="w-full">

          <div class="mb-5 text-center md:text-left">
            <img src="{{ asset('images/logo-squadtix.png') }}" alt="Squadtix Logo"
                 class="h-10 object-contain mb-2 mx-auto md:mx-0">
            <h2 class="text-lg md:text-xl font-semibold text-light-50 tracking-tight">
              Gate Staff Login
            </h2>
            <p class="mt-1 text-xs text-light-400">
              Login to start scanning and validating tickets.
            </p>
          </div>

          <div class="rounded-2xl border border-light-800/80 bg-light-900/80 backdrop-blur-xl shadow-soft-xl p-5 md:p-6">

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
              @csrf

              <div>
                <label class="block text-xs font-medium text-light-200 mb-1">Email</label>
                <input type="email" name="email" required
                       class="block w-full rounded-lg border border-light-700 bg-light-900/60 px-3 py-2.5 text-sm text-light-50 focus:ring-brand-blue focus:border-brand-blue outline-none">
              </div>

              <div>
                <label class="block text-xs font-medium text-light-200 mb-1">Password</label>
                <input type="password" name="password" required
                       class="block w-full rounded-lg border border-light-700 bg-light-900/60 px-3 py-2.5 text-sm text-light-50 focus:ring-brand-blue focus:border-brand-blue outline-none">
              </div>

              <button type="submit"
                class="w-full rounded-xl bg-gradient-to-r from-brand-blue to-brand-cyan px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:brightness-110 active:scale-[0.98] transition">
                Login & Open Gate
              </button>
            </form>

            <p class="mt-4 text-[10px] text-light-500 text-center md:text-left">
              Authorized event staff only.
            </p>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>
</body>
</html>
