<template>
  <div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Products</h2>
    <div class="space-y-2" ref="productList">
      <div v-for="product in products" :key="product.id" class="p-2 border-b flex justify-between items-center">
        <div>
          <span class="font-medium">{{ product.name }}</span>
          <span class="ml-2 text-gray-600">SKU: {{ product.sku }}</span>
          <span class="ml-2 text-gray-600">Stock: {{ product.quantity }}</span>
          <span class="ml-2 text-gray-600">Price: ${{ product.price }}</span>
        </div>
        <div>
          <button @click="$emit('edit', product)" class="bg-yellow-500 text-white px-2 py-1 rounded mr-2 hover:bg-yellow-600">
            Edit
          </button>
          <button @click="$emit('delete', product.id)" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductListComponent',
  props: {
    products: {
      type: Array,
      required: true
    }
  }
};
</script>
