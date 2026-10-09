<footer>
    <div class="bg-(--color-primary) text-white">
        <div class="wrapper">
            <div class="py-3 lg:py-4">
                <div class="flex flex-col gap-2 min-[400px]:flex-row min-[400px]:gap-0 justify-between items-center">
                    <x-logo class="footer-logo" />

                    <p class="text-[13px] lg:text-sm">&#169; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
