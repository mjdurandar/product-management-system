<x-app-layout>
  <x-slot name="header">
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product') }}
        </h2>
        <div class="flex gap-4">
            <select id="apiSelector" class="form-control">
                <option value="platzi">Platzi API</option>
                <option value="fakestore">Fake Store API</option>
            </select>
            <button class="btn btn-success" data-toggle="modal" data-target="#productModal">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
      </div>
  </x-slot>

  <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                  <div class="space-y-4" id="products-container">
                      @foreach($products as $product)
                          <div class="bg-white rounded-lg shadow-md p-4">
                              <div class="flex gap-4">
                              <div class="w-48 flex-shrink-0">
                                <img src="{{ $product['image'] ?? ($product['images'][0] ?? asset('images/img-placeholder.jpg')) }}" 
                                        alt="{{ $product['title'] ?? 'Product Image' }}" 
                                        class="w-full h-48 object-cover rounded-md"
                                        onerror="this.onerror=null; this.src='{{ asset('images/img-placeholder.jpg') }}';">
                                </div>
                                  <div class="flex-grow">
                                      <h3 class="text-lg font-semibold mb-2">{{ $product['title'] }}</h3>
                                      <p class="text-gray-600 mb-4">{{ \Str::limit($product['description'], 200) }}</p>
                                      <div class="flex justify-between items-center">
                                          <span class="text-lg font-bold">${{ $product['price'] }}</span>
                                          <div>
                                            <button class="btn btn-primary btn-sm edit-product" 
                                                data-id="{{ $product['id'] }}" 
                                                data-title="{{ $product['title'] }}" 
                                                data-price="{{ $product['price'] }}" 
                                                data-description="{{ $product['description'] }}" 
                                                data-image="{{ $product['image'] ?? ($product['images'][0] ?? '') }}" 
                                                data-category="{{ isset($product['category']) ? (is_array($product['category']) ? $product['category']['id'] : $product['category']) : '' }}">Edit</button>
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                          </div>        
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Product Modal -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="categorySelect" name="category_id">
                            <option value="">Select a category</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="images">Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProductForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Product Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">Price</label>
                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Category</label>
                        <select class="form-control" id="edit_category" name="categoryId">
                            <option value="">Select a category</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit_images">New Image (optional)</label>
                        <input type="file" class="form-control" id="edit_images" name="images[]" accept="image/*">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="edit_current_image" class="mt-2">
                        <label>Current Image:</label>
                        <img src="" alt="Current product image" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
  </div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Add event listener for modal open
    $('#productModal').on('show.bs.modal', function () {
        fetchCategories();
    });

    // Add event listener for edit modal open
    $('#editProductModal').on('show.bs.modal', function () {
        fetchCategories();
    });

    let fetchCategories = async () => {
        const response = await fetch("{{ route('categories') }}");
        const data = await response.json();
        
        // Update Add Product modal dropdown
        let categorySelect = document.getElementById('categorySelect');
        let editCategorySelect = document.getElementById('edit_category');
        
        // Clear both dropdowns
        categorySelect.innerHTML = '<option value="">Select a category</option>';
        editCategorySelect.innerHTML = '<option value="">Select a category</option>';
        
        // Add categories to both dropdowns
        data.forEach(category => {
            // For Add Product modal
            let option = document.createElement('option');
            option.value = category.id; 
            option.textContent = category.name || category; 
            categorySelect.appendChild(option);
            
            // For Edit Product modal
            let editOption = document.createElement('option');
            editOption.value = category.id;
            editOption.textContent = category.name || category;
            editCategorySelect.appendChild(editOption);
        });

        return data;
    };

    const showLoading = () => {
        const productsContainer = document.querySelector('.space-y-4');
        productsContainer.innerHTML = `
            <div class="flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
            </div>
        `;
    };

    const apiSelector = document.getElementById('apiSelector');
    
    // Function to attach edit button event listeners
    function attachEditButtonListeners() {
        document.querySelectorAll('.edit-product').forEach(button => {
            // Remove existing listener to prevent duplicates
            button.removeEventListener('click', handleEditButtonClick);
            // Add new listener
            button.addEventListener('click', handleEditButtonClick);
        });
    }

    // Handler function for edit button clicks
    function handleEditButtonClick() {
        const modal = document.getElementById('editProductModal');
        const form = document.getElementById('editProductForm');
        const productId = this.dataset.id;
        
        // Reset any previous error states
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
            el.nextElementSibling.textContent = '';
        });
        
        // Set form action
        form.action = "{{ url('/product') }}/" + productId;
        
        // Fill form with product data
        document.getElementById('edit_title').value = this.dataset.title;
        document.getElementById('edit_price').value = this.dataset.price;
        document.getElementById('edit_description').value = this.dataset.description;
        
        // Handle current image
        const currentImage = document.querySelector('#edit_current_image img');
        if (this.dataset.image) {
            currentImage.src = this.dataset.image;
            document.getElementById('edit_current_image').style.display = 'block';
        } else {
            document.getElementById('edit_current_image').style.display = 'none';
        }
        
        // Set category if available
        const categorySelect = document.getElementById('edit_category');
        const category = this.dataset.category;
        if (category) {
            Array.from(categorySelect.options).forEach(option => {
                if (option.value === category) {
                    option.selected = true;
                }
            });
        }
        
        // Show modal
        $(modal).modal('show');
    }

    apiSelector.addEventListener('change', function() {
        showLoading();
        
        fetch("{{ route('switch-api') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ api: this.value })
        })
        .then(response => response.json())
        .then(data => {
            const productsContainer = document.querySelector('.space-y-4');
            productsContainer.innerHTML = data.products.map(product => createProductElement(product)).join('');
            
            // Reattach event listeners to the new buttons
            attachEditButtonListeners();
            
            // Refresh categories after API switch
            fetchCategories();
        })
        .catch(error => console.error('Error switching API:', error));
    });

    function createProductElement(product) {
        return `
            <div class="bg-white rounded-lg shadow-md p-4" data-product-id="${product.id}">
                <div class="flex gap-4">
                    <div class="w-48 flex-shrink-0">
                        <img src="${product.image ?? (product.images?.[0] ?? '/images/placeholder.jpg')}" 
                             alt="${product.title ?? product.name ?? 'Product Image'}" 
                             class="w-full h-48 object-cover rounded-md"
                             onerror="this.onerror=null; this.className+=' placeholder-image'; this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';">
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold mb-2">${product.title ?? product.name}</h3>
                        <p class="text-gray-600 mb-4">${product.description.slice(0, 200)}...</p>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold">$${product.price}</span>
                            <div>
                            <button class="btn btn-primary btn-sm edit-product" 
                                data-id="${product.id}" 
                                data-title="${product.title}" 
                                data-price="${product.price}" 
                                data-description="${product.description}" 
                                data-image="${product.image ?? (product.images?.[0] ?? '')}" 
                                data-category="${product.category ? (Array.isArray(product.category) ? product.category[0].id : product.category) : ''}">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Handle edit form submission
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = this.action;
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Updating...';
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server error occurred');
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Close modal
            $('#editProductModal').modal('hide');

            // Show success message with SweetAlert2
            Swal.fire({
                title: 'Success!',
                text: 'Product updated successfully',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                didClose: () => {
                    // Refresh page after alert closes
                    location.reload();
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Show error message with SweetAlert2
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while updating the product',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            
            // Handle validation errors
            if (error.errors) {
                Object.keys(error.errors).forEach(field => {
                    const input = this.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = error.errors[field][0];
                        }
                    }
                });
            }
        })
        .finally(() => {
            // Reset button state
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });

    // Initial attachment of event listeners
    attachEditButtonListeners();

    // Initial categories fetch
    fetchCategories().catch(error => console.error('Error fetching categories:', error));
  });
</script>

<style>
.placeholder-image {
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.placeholder-image::before {
    content: '📷';
    font-size: 2rem;
    color: #9ca3af;
}
</style>

</x-app-layout>
