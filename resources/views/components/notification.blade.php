@if (session('success') || session('error'))
    <div id="notification-overlay" class="fixed inset-0 bg-black/50 z-99999 flex items-center justify-center"
        onClick="closeNotification()">
        <div class="bg-(--body-bg) text-center p-5 sm:p-8 w-[90%] sm:w-lg" onClick="event.stopPropagation()">

            @if (session('success'))
                <i class="bi bi-check-circle-fill text-(--color-primary) block text-3xl md:text-4xl mb-2 md:mb-4"></i>
                <p class="text-xl md:text-2xl font-semibold mb-2 md:mb-3">{{ session('success') }}</p>
            @else
                <i class="bi bi-x-circle-fill text-(--color-red) block text-3xl md:text-5xl mb-2 md:mb-4"></i>
                <p class="text-xl md:text-2xl font-semibold mb-2 md:mb-3">{{ session('error') }}</p>
            @endif

            <p class="text-base mb-4 md:mb-5">{{ session('message') }}</p>

            <button type="button" onClick="closeNotification()"
                class="px-4 py-1 text-base bg-(--color-bg-button) text-(--color-primary) font-medium tracking-[0.5px] cursor-pointer transition duration-300 hover:bg-(--color-bg-button-hover) active:bg-(--color-bg-button-active) active:scale-95">
                Close
            </button>
        </div>
    </div>

    <script>
        function closeNotification() {
            const overlay = document.getElementById('notification-overlay');
            if (overlay) overlay.remove();
        }

        setTimeout(closeNotification, 5000);
    </script>
@endif
