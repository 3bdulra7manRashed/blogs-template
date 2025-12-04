<!-- Add Tag Modal -->
<div id="tagModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">إضافة وسم جديد</h3>
            <button type="button" onclick="closeTagModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="quick-tag-form">
                <div class="space-y-4">
                    <!-- Tag Name -->
                    <div>
                        <label for="modal-tag-name" class="block text-sm font-medium text-gray-700 mb-1">اسم الوسم</label>
                        <input type="text" id="modal-tag-name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent"
                               placeholder="أدخل اسم الوسم">
                        <p id="modal-tag-error" class="text-sm text-red-600 mt-1 hidden"></p>
                    </div>

                    <!-- Tag Slug (Optional) -->
                    <div>
                        <label for="modal-tag-slug" class="block text-sm font-medium text-gray-700 mb-1">الرابط الدائم (اختياري)</label>
                        <input type="text" id="modal-tag-slug" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent"
                               placeholder="سيتم توليده تلقائياً">
                        <p class="text-xs text-gray-500 mt-1">سيتم توليده تلقائياً من الاسم إذا ترك فارغاً</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 bg-gray-50">
            <button type="button" onclick="saveTag()" class="px-5 py-2.5 bg-brand-accent text-white rounded-lg hover:bg-opacity-90 font-medium transition-colors">
                حفظ الوسم
            </button>
            <button type="button" onclick="closeTagModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
        </div>
    </div>
</div>

<script>
    // Tag Modal Functions
    function openTagModal() {
        const modal = document.getElementById('tagModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('modal-tag-name').focus();
    }

    function closeTagModal() {
        const modal = document.getElementById('tagModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Clear form
        document.getElementById('modal-tag-name').value = '';
        document.getElementById('modal-tag-slug').value = '';
        document.getElementById('modal-tag-error').classList.add('hidden');
        document.getElementById('modal-tag-error').textContent = '';
    }

    async function saveTag() {
        const name = document.getElementById('modal-tag-name').value.trim();
        const slug = document.getElementById('modal-tag-slug').value.trim();
        const errorEl = document.getElementById('modal-tag-error');
        
        // Validation
        if (!name) {
            errorEl.textContent = 'اسم الوسم مطلوب';
            errorEl.classList.remove('hidden');
            return;
        }
        
        errorEl.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route("admin.tags.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    slug: slug || null
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Add new tag to the select dropdown
                const tagsSelect = document.getElementById('tags');
                const newOption = document.createElement('option');
                newOption.value = data.tag.id;
                newOption.textContent = data.tag.name;
                newOption.selected = true;
                tagsSelect.insertBefore(newOption, tagsSelect.firstChild);
                
                // Show success message
                showNotification('تم إضافة الوسم بنجاح!', 'success');
                
                // Close modal
                closeTagModal();
            } else {
                errorEl.textContent = data.message || 'حدث خطأ أثناء الحفظ';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            errorEl.textContent = 'حدث خطأ أثناء الاتصال بالخادم';
            errorEl.classList.remove('hidden');
        }
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('tagModal').style.display === 'flex') {
            closeTagModal();
        }
    });

    // Close modal on background click
    document.getElementById('tagModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTagModal();
        }
    });
</script>

