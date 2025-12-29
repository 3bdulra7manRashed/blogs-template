<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">تأكيد الإجراء</h3>
            <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-gray-700 text-base leading-relaxed" id="deleteModalMessage">
                        <!-- Message will be set by JavaScript -->
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 bg-gray-50">
            <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
            <button type="button" onclick="confirmDelete()" class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                تأكيد
            </button>
        </div>
    </div>
</div>

<script>
    let deleteFormToSubmit = null;

    function showDeleteModal(form, message) {
        deleteFormToSubmit = form;
        document.getElementById('deleteModalMessage').textContent = message;
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        deleteFormToSubmit = null;
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function confirmDelete() {
        if (deleteFormToSubmit) {
            deleteFormToSubmit.submit();
        }
        closeDeleteModal();
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });

    // Close modal on background click
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>

