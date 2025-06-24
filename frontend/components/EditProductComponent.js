import { defineComponent } from 'vue';

export default defineComponent({
    name: 'EditProductComponent',
    props: ['product'],
    template: `
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded shadow w-1/2">
                <h3 class="text-lg font-medium mb-2">Edit Product</h3>
                <div class="grid grid-cols-2 gap-4">
                    <input v-model="product.name" placeholder="Name" class="p-2 border rounded">
                    <input v-model="product.sku" placeholder="SKU" class="p-2 border rounded">
                    <input v-model="product.category" placeholder="Category" class="p-2 border rounded">
                    <input v-model="product.price" type="number" placeholder="Price" class="p-2 border rounded">
                    <input v-model="product.quantity" type="number" placeholder="Quantity" class="p-2 border rounded">
                    <input v-model="product.minimum_stock" type="number" placeholder="Minimum Stock" class="p-2 border rounded">
                    <input v-model="product.supplier_id" type="number" placeholder="Supplier ID" class="p-2 border rounded">
                    <input v-model="product.description" placeholder="Description" class="p-2 border rounded">
                </div>
                <div class="mt-4 flex justify-end">
                    <button @click="$emit('close')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600">Cancel</button>
                    <button @click="updateProduct" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                </div>
            </div>
        </div>
    `,
    methods: {
        async updateProduct() {
            try {
                const response = await fetch(`/api/products/${this.product.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(this.product)
                });
                const data = await response.json();
                alert(data.message || data.error);
                if (data.message) {
                    this.$emit('update', this.product);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }
});