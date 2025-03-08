<x-app-layout>
  <x-slot name="header">
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product') }}
        </h2>
        <button class="btn btn-success" data-toggle="modal" data-target="#productModal"><i class="fa-solid fa-plus"></i></button>
      </div>
  </x-slot>

  <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">
                  {{ __("You're logged in!") }}
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
    fetch("{{ route('categories') }}") // Use Laravel's route helper
      .then(response => response.json())
      .then(data => {
          let categorySelect = document.getElementById('categorySelect');
          categorySelect.innerHTML = '<option value="">Select a category</option>'; // Reset options
          data.forEach(category => {
              let option = document.createElement('option');
              option.value = category.id; 
              option.textContent = category.name || category; // Platzi uses `name`, FakeStore returns plain text
              categorySelect.appendChild(option);
          });
      })
      .catch(error => console.error('Error fetching categories:', error));
  });
</script>


</x-app-layout>
