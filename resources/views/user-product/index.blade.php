<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Products') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($userProducts->isEmpty())
                    <div class="text-center py-8">
                        <h3 class="text-lg font-medium text-gray-500">You haven't claimed any products yet.</h3>
                        <a href="{{ route('available-product.index') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Browse Available Products
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($userProducts as $product)
                            <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
                                <div class="flex flex-col h-full">
                                    <div class="relative pb-48 overflow-hidden">
                                        <img src="{{ $product->image ?? asset('images/img-placeholder.jpg') }}" 
                                             alt="{{ $product->title }}" 
                                             class="absolute inset-0 h-full w-full object-cover rounded-t-lg"
                                             onerror="this.onerror=null; this.src='{{ asset('images/img-placeholder.jpg') }}';">
                                    </div>
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h3 class="text-xl font-semibold mb-2">{{ $product->title }}</h3>
                                        <p class="text-gray-600 mb-4 flex-grow">{{ \Str::limit($product->description, 150) }}</p>
                                        <div class="flex justify-between items-center mt-4">
                                            <span class="text-lg font-bold">${{ $product->price }}</span>
                                            <span class="text-sm text-gray-500">Claimed on {{ $product->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 