<div x-data="releaseNotes()" x-init="init()">
  <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="dismiss()"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">What's new in {{ config('app.version') }}</h3>
        <button class="text-gray-400 hover:text-gray-600" @click="dismiss()"><i class="fas fa-times"></i></button>
      </div>
      <div class="px-5 py-4 space-y-3 text-sm text-gray-700 max-h-[60vh] overflow-y-auto">
        @if(file_exists(base_path('..'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'CHANGELOG.html')))
          {!! file_get_contents(base_path('..'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'CHANGELOG.html')) !!}
        @else
          <ul class="list-disc pl-5">
            <li>Welcome to the latest version of the application.</li>
            <li>UI improvements and bug fixes.</li>
            <li>Performance enhancements.</li>
          </ul>
        @endif
      </div>
      <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-end">
        <label class="mr-auto inline-flex items-center text-sm text-gray-600">
          <input type="checkbox" class="mr-2 rounded border-gray-300" x-model="dontShowAgain" />
          Don't show again for this version
        </label>
        <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md" @click="dismiss()">Got it</button>
      </div>
    </div>
  </div>

  <script>
    function releaseNotes() {
      const version = @js(config('app.version'));
      const key = 'release_notes_seen_' + version;
      return {
        open: false,
        dontShowAgain: true,
        init() {
          try {
            const seen = localStorage.getItem(key);
            this.open = !seen;
          } catch (e) { this.open = true; }
        },
        dismiss() {
          if (this.dontShowAgain) {
            try { localStorage.setItem(key, '1'); } catch (e) {}
          }
          this.open = false;
        }
      }
    }
  </script>
</div>


