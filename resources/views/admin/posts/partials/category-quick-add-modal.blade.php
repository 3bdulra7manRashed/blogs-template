<!-- Add Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">إضافة قسم جديد</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="quick-category-form">
                <div class="space-y-4">
                    <!-- Category Name -->
                    <div>
                        <label for="modal-category-name" class="block text-sm font-medium text-gray-700 mb-1">اسم القسم</label>
                        <input type="text" id="modal-category-name" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent"
                               placeholder="أدخل اسم القسم">
                        <p id="modal-category-error" class="text-sm text-red-600 mt-1 hidden"></p>
                    </div>

                    <!-- Category Slug (Optional) -->
                    <div>
                        <label for="modal-category-slug" class="block text-sm font-medium text-gray-700 mb-1">الرابط الدائم (اختياري)</label>
                        <input type="text" id="modal-category-slug" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent"
                               placeholder="سيتم توليده تلقائياً">
                        <p class="text-xs text-gray-500 mt-1">سيتم توليده تلقائياً من الاسم إذا ترك فارغاً</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 bg-gray-50">
            <button type="button" onclick="saveCategory()" class="px-5 py-2.5 bg-brand-accent text-white rounded-lg hover:bg-opacity-90 font-medium transition-colors">
                حفظ القسم
            </button>
            <button type="button" onclick="closeCategoryModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                إلغاء
            </button>
        </div>
    </div>
</div>

<script>
    // Category Modal Functions
    function openCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('modal-category-name').focus();
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Clear form
        document.getElementById('modal-category-name').value = '';
        document.getElementById('modal-category-slug').value = '';
        document.getElementById('modal-category-error').classList.add('hidden');
        document.getElementById('modal-category-error').textContent = '';
    }

    async function saveCategory() {
        const name = document.getElementById('modal-category-name').value.trim();
        const slug = document.getElementById('modal-category-slug').value.trim();
        const errorEl = document.getElementById('modal-category-error');
        
        // Validation
        if (!name) {
            errorEl.textContent = 'اسم القسم مطلوب';
            errorEl.classList.remove('hidden');
            return;
        }
        
        errorEl.classList.add('hidden');
        
        try {
            const response = await fetch('{{ route("admin.categories.store") }}', {
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
                // Add new category to the list
                const categoriesList = document.getElementById('categories-list');
                const newCategoryHtml = `
                    <label class="flex items-center space-x-2 space-x-reverse cursor-pointer hover:bg-gray-50 p-1 rounded animate-fade-in">
                        <input type="checkbox" name="categories[]" value="${data.category.id}" checked
                               class="rounded border-gray-300 text-brand-accent focus:ring-brand-accent h-4 w-4">
                        <span class="text-sm text-gray-700 select-none">${data.category.name}</span>
                    </label>
                `;
                categoriesList.insertAdjacentHTML('afterbegin', newCategoryHtml);
                
                // Show success message
                showNotification('تم إضافة القسم بنجاح!', 'success');
                
                // Close modal
                closeCategoryModal();
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

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-medium`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('categoryModal').style.display === 'flex') {
            closeCategoryModal();
        }
    });

    // Close modal on background click
    document.getElementById('categoryModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });
</script>

