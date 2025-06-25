import { createRouter, createWebHistory } from 'vue-router'
import Admin from '../views/Admin.vue'
import Inventory from '../views/Inventory.vue'
import Orders from '../views/Orders.vue'
import Products from '../views/Products.vue'
import Suppliers from '../views/Suppliers.vue'
import Users from '../views/Users.vue'
import MyLogin from '../views/MyLogin.vue'

const routes = [
  { path: '/', component: MyLogin }, // Default route to login page
  { path: '/admin', component: Admin },
  { path: '/inventory', component: Inventory },
  { path: '/orders', component: Orders },
  { path: '/products', component: Products },
  { path: '/suppliers', component: Suppliers },
  { path: '/users', component: Users },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router