<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available Products') }}
            </h2>
            <div class="flex gap-4">
                <select id="apiSelector" class="form-control">
                    <option value="platzi">Platzi API</option>
                    <option value="fakestore">Fake Store API</option>
                </select>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="products-container">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
                            <div class="flex flex-col h-full">
                                <div class="relative pb-48 overflow-hidden">
                                    <img src="{{ $product['image'] ?? ($product['images'][0] ?? asset('images/img-placeholder.jpg')) }}" 
                                         alt="{{ $product['title'] ?? 'Product Image' }}" 
                                         class="absolute inset-0 h-full w-full object-cover rounded-t-lg"
                                         onerror="this.onerror=null; this.src='{{ asset('images/img-placeholder.jpg') }}';">
                                </div>
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="text-xl font-semibold mb-2">{{ $product['title'] }}</h3>
                                    <p class="text-gray-600 mb-4 flex-grow">{{ \Str::limit($product['description'], 150) }}</p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-lg font-bold">${{ $product['price'] }}</span>
                                        <button class="claim-product bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
                                                data-id="{{ $product['id'] }}">
                                            Get Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiSelector = document.getElementById('apiSelector');
            const productsContainer = document.getElementById('products-container');

            // Update API selection
            apiSelector.addEventListener('change', async function() {
                showLoading();
                
                try {
                    const response = await fetch("{{ route('switch-api') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ api: this.value })
                    });

                    const data = await response.json();
                    location.reload(); // Reload to show new products
                } catch (error) {
                    console.error('Error switching API:', error);
                }
            });

            // Claim product functionality
            document.querySelectorAll('.claim-product').forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = this.dataset.id;
                    
                    try {
                        const response = await fetch(`/available-products/${productId}/claim`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();
                        
                        if (response.ok) {
                            alert('Product claimed successfully!');
                            // You might want to update the UI here
                        } else {
                            throw new Error(data.message || 'Failed to claim product');
                        }
                    } catch (error) {
                        alert(error.message);
                    }
                });
            });

            const showLoading = () => {
                productsContainer.innerHTML = `
                    <div class="col-span-full flex justify-center items-center p-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                    </div>
                `;
            };
        });
    </script>
    @endpush
</x-app-layout>
