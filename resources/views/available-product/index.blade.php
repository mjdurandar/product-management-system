<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available Products') }}
            </h2>
            <div class="flex gap-4">
                <select id="apiSelector" class="form-control rounded-md border-gray-300">
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
                        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex flex-col h-full">
                                <div class="relative pb-48 overflow-hidden rounded-t-lg">
                                    <img src="{{ $product['image'] ?? ($product['images'][0] ?? asset('images/img-placeholder.jpg')) }}" 
                                         alt="{{ $product['title'] ?? 'Product Image' }}" 
                                         class="absolute inset-0 h-full w-full object-cover"
                                         onerror="this.onerror=null; this.src='{{ asset('images/img-placeholder.jpg') }}'">
                                </div>
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="text-xl font-semibold mb-2 text-gray-800">{{ $product['title'] }}</h3>
                                    <p class="text-gray-600 mb-4 flex-grow">{{ \Str::limit($product['description'], 150) }}</p>
                                    <div class="flex justify-between items-center mt-4">
                                        <span class="text-2xl font-bold text-indigo-600">${{ $product['price'] }}</span>
                                        <button type="button" 
                                                class="btn btn-success claim-product bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-lg transform hover:scale-105 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2"
                                                data-id="{{ $product['id'] }}">
                                            <span>Get Product</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
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
                    const button = this;
                    
                    try {
                        button.disabled = true;
                        button.classList.add('opacity-75', 'cursor-not-allowed');
                        button.innerHTML = `<span>Claiming...</span>
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>`;
                        
                        const response = await fetch(`/available-products/${productId}/claim`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();
                        
                        if (response.ok) {
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down';
                            successMessage.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Product claimed successfully!</span>
                                </div>
                            `;
                            document.body.appendChild(successMessage);
                            
                            // Remove the message after 3 seconds
                            setTimeout(() => {
                                successMessage.remove();
                                // Reload the page to update the product list
                                location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Failed to claim product');
                        }
                    } catch (error) {
                        // Show error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down';
                        errorMessage.innerHTML = `
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <span>${error.message}</span>
                            </div>
                        `;
                        document.body.appendChild(errorMessage);
                        
                        // Remove the error message after 3 seconds
                        setTimeout(() => {
                            errorMessage.remove();
                        }, 3000);
                    } finally {
                        // Reset button state
                        button.disabled = false;
                        button.classList.remove('opacity-75', 'cursor-not-allowed');
                        button.innerHTML = `
                            <span>Get Product</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                            </svg>
                        `;
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

    <style>
        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
    </style>
    @endpush
</x-app-layout>
