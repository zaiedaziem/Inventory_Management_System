import { defineComponent } from 'vue';

export default defineComponent({
    name: 'RegisterComponent',
    template: `
        <div class="mb-8 bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Register New Staff</h2>
            <div class="space-y-4">
                <input v-model="registerData.name" placeholder="Name" class="w-full p-2 border rounded">
                <input v-model="registerData.email" type="email" placeholder="Email" class="w-full p-2 border rounded">
                <input v-model="registerData.password" type="password" placeholder="Password" class="w-full p-2 border rounded">
                <input v-model="registerData.department" placeholder="Department" class="w-full p-2 border rounded">
                <button @click="register" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Register</button>
            </div>
        </div>
    `,
    data() {
        return {
            registerData: { name: '', email: '', password: '', department: '' }
        };
    },
    methods: {
        async register() {
            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ...this.registerData,
                        role: 'staff'
                    })
                });
                const data = await response.json();
                alert(data.message || data.error);
                if (data.message) {
                    this.$emit('login');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }
});