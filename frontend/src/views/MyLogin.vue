<template>
  <div>
    <div class="login-container">
      <h2>Login</h2>
      <form @submit.prevent="login">
        <div class="form-row">
          <label for="username">Username:</label>
          <input v-model="username" type="text" id="username" required />
        </div>
        <div class="form-row">
          <label for="password">Password:</label>
          <input v-model="password" type="password" id="password" required />
        </div>
        <button type="submit">Login</button>
        <p class="register-link">
          Don't have an account?
          <router-link to="/register">Register here</router-link>
        </p>
      </form>
    </div>
    <p v-if="successMessage" class="success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
export default {
  name: 'MyLogin',
  data() {
    return {
      username: '',
      password: '',
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async login() {
      this.errorMessage = '';
      this.successMessage = '';
      try {
        const res = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            username: this.username,
            password: this.password
          })
        });
        const data = await res.json();
        if (res.ok && data.token) {
          this.successMessage = `Login successful! Welcome, ${data.user.username}. Redirecting...`;
          localStorage.setItem('jwt_token', data.token);
          localStorage.setItem('user_info', JSON.stringify(data.user));
          this.$router.push('/dashboard');
        } else {
          this.errorMessage = data.error || 'Login failed. Please check your credentials.';
        }
      } catch (err) {
        this.errorMessage = 'Network error. Could not connect to the server. Please try again.';
      }
    }
  }
};
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 20px auto;
  padding: 30px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}
h2 {
  text-align: center;
  color: #333;
  margin-bottom: 25px;
  font-size: 24px;
}
.form-row {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}
.form-row label {
  width: 120px;
  margin-right: 15px;
  font-weight: bold;
  text-align: right;
  color: #555;
}
.form-row input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-sizing: border-box;
  font-size: 16px;
}
button {
  width: 100%;
  padding: 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  margin-top: 20px;
}
button:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}
.error {
  color: #dc3545;
  margin-top: 15px;
  text-align: center;
  font-weight: bold;
}
.success {
  color: #28a745;
  margin-top: 15px;
  text-align: center;
  font-weight: bold;
}
.register-link {
  margin-top: 25px;
  text-align: center;
  font-size: 15px;
  color: #666;
}
.register-link a {
  color: #007bff;
  text-decoration: none;
  font-weight: bold;
}
.register-link a:hover {
  text-decoration: underline;
}
</style>