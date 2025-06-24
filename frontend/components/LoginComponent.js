import { defineComponent } from 'vue';

export default defineComponent({
    name: 'LoginComponent',
    template: `
        <div class="mb-8 bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold mb-4">Login</h2>
            <div class="space-y-4">
                <input v-model="loginData.email" type="email" placeholder="Email" class="w-full p-2 border rounded">
                <input v-model="loginData.password" type="password" placeholder="Password" class="w-full p-2 border rounded">
                <button @click="login" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Login</button>
            </div>
        </div>
    `,
    data() {
        return {
            loginData: { email: '', password: '' }
        };
    },
    methods: {
        async login() {
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.loginData)
                });
                const data = await response.json();
                if (data.token) {
                    localStorage.setItem('token', data.token);
                    this.$emit('login');
                    alert('Login successful!');
                } else {
                    alert(data.error || 'Login failed');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
    }
});