import { defineComponent } from 'vue';

export default defineComponent({
    name: 'AddProductComponent',
    template: `
        <div class="mb-4 bg-white p-6 rounded shadow">
            <h3 class="text-lg font-medium mb-2">Add New Product</h3>
            <div class="grid grid-cols-2 gap-4">
                <input v-model="newProduct.name" placeholder="Name" class="p-2 border rounded">
                <input v-model="newProduct.sku" placeholder="SKU" class="p-2 border rounded">
                <input v-model="newProduct.category" placeholder="Category" class="p-2 border rounded">
                <input v-model="newProduct.price" type="number" placeholder="Price" class="p-2 border rounded">
                <input v-model="newProduct.quantity" type="number" placeholder="Quantity" class="p-2 border rounded">
                <input v-model="newProduct.minimum_stock" type="number" placeholder="Minimum Stock" class="p-2 border rounded">
                <input v-model="newProduct.supplier_id" type="number" placeholder="Supplier ID" class="p-2 border rounded">
                <input v-model="newProduct.description" placeholder="Description" class="p-2 border rounded">
            </div>
            <button @click="addProduct" class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Add Product</button>
        </div>
    `,
    data() {
        return {
            newProduct: {
                name: '', sku: '', category: '', price: 0, quantity: 0,
                minimum_stock: 10, supplier_id: null, description: ''
            }
        };
    },
    methods: {
        async addProduct() {
            try {
                const response = await fetch('/api/products', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(this.newProduct)
                });
                const data = await response.json();
                alert(data.message || data.error);
                if (data.message) {
                    this.newProduct = {
                        name: '', sku: '', category: '', price: 0, quantity: 0,
                        minimum_stock: 10, supplier_id: null, description: ''
                    };
                    this.$emit('product-added');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }
});