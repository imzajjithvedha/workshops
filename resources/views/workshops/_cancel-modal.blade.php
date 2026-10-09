<div id="cancel-modal" class="fixed inset-0 z-99999 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-2 p-3 text-center sm:mx-4 sm:p-8" role="dialog" aria-modal="true" aria-labelledby="cancel-title">
        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3 sm:mb-5">
            <i class="bi bi-person-x text-red-500 text-2xl"></i>
        </div>
        <p id="cancel-title" class="text-xl font-semibold text-(--color-primary) mb-0.75 sm:mb-2 sm:text-2xl">Cancel this registration?</p>
        <p class="text-[15px] text-(--color-primary) leading-relaxed mb-4 sm:mb-6 sm:text-base">
            <span id="cancel-attendee" class="font-semibold"></span>'s seat will be freed. The registration stays in the history with your name on it.
        </p>

        <form id="cancel-form" action="" method="POST" class="text-left">
            @csrf
            @method('PATCH')
            <label class="label" for="reason">Reason (optional)</label>
            <textarea id="reason" name="reason" rows="2" maxlength="255" class="textarea mb-4 sm:mb-6" placeholder="e.g. Called to cancel"></textarea>

            <div class="flex gap-3">
                <button type="button" id="cancel-close"
                    class="flex-1 py-3.5 border border-(--color-light-gray) text-base font-medium text-(--color-primary) cursor-pointer transition duration-300 hover:bg-(--color-light-white)">
                    Keep It
                </button>
                <button type="submit"
                    class="flex-1 py-3.5 bg-(--color-red) text-white text-base font-medium cursor-pointer transition duration-300 hover:bg-red-800">
                    <i class="bi bi-x-circle mr-1"></i> Cancel Registration
                </button>
            </div>
        </form>
    </div>
</div>
