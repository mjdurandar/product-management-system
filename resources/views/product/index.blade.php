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
                  <div class="space-y-4">
                      @foreach($products as $product)
                          <div class="bg-white rounded-lg shadow-md p-4">
                              <div class="flex gap-4">
                                  <div class="w-48 flex-shrink-0">
                                      <img src="{{ $product['image'] ?? ($product['images'][0] ?? '') }}" 
                                           alt="{{ $product['title'] }}" 
                                           class="w-full h-48 object-cover rounded-md">
                                  </div>
                                  <div class="flex-grow">
                                      <h3 class="text-lg font-semibold mb-2">{{ $product['title'] }}</h3>
                                      <p class="text-gray-600 mb-4">{{ \Str::limit($product['description'], 200) }}</p>
                                      <div class="flex justify-between items-center">
                                          <span class="text-lg font-bold">${{ $product['price'] }}</span>
                                          <button class="btn btn-primary btn-sm">View Details</button>
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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const fetchCategories = () => {
        fetch("{{ route('categories') }}") 
            .then(response => response.json())
            .then(data => {
                let categorySelect = document.getElementById('categorySelect');
                categorySelect.innerHTML = '<option value="">Select a category</option>';
                data.forEach(category => {
                    let option = document.createElement('option');
                    option.value = category.id; 
                    option.textContent = category.name || category; 
                    categorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching categories:', error));
    };

    const apiSelector = document.getElementById('apiSelector');
    apiSelector.addEventListener('change', function() {
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
            // Update the products on the page
            const productsContainer = document.querySelector('.space-y-4');
            productsContainer.innerHTML = data.products.map(product => `
                <div class="bg-white rounded-lg shadow-md p-4">
                    <div class="flex gap-4">
                        <div class="w-48 flex-shrink-0">
                            <img src="${product.image ?? (product.images?.[0] ?? '')}" 
                                 alt="${product.title ?? product.name}" 
                                 class="w-full h-48 object-cover rounded-md">
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold mb-2">${product.title ?? product.name}</h3>
                            <p class="text-gray-600 mb-4">${product.description.slice(0, 200)}...</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold">$${product.price}</span>
                                <button class="btn btn-primary btn-sm">View Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // Refresh categories after API switch
            fetchCategories();
        })
        .catch(error => console.error('Error switching API:', error));
    });

    fetchCategories();
  });
</script>

</x-app-layout>
