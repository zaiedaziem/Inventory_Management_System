import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    inventory: [],
    products: [],
    orders: [],
    suppliers: [],
    users: [],
  },
  mutations: {
    setInventory(state, inventory) {
      state.inventory = inventory;
    },
    setProducts(state, products) {
      state.products = products;
    },
    setOrders(state, orders) {
      state.orders = orders;
    },
    setSuppliers(state, suppliers) {
      state.suppliers = suppliers;
    },
    setUsers(state, users) {
      state.users = users;
    },
  },
  actions: {
    fetchInventory({ commit }) {
      // Fetch inventory data from API and commit to state
    },
    fetchProducts({ commit }) {
      // Fetch products data from API and commit to state
    },
    fetchOrders({ commit }) {
      // Fetch orders data from API and commit to state
    },
    fetchSuppliers({ commit }) {
      // Fetch suppliers data from API and commit to state
    },
    fetchUsers({ commit }) {
      // Fetch users data from API and commit to state
    },
  },
  getters: {
    getInventory: state => state.inventory,
    getProducts: state => state.products,
    getOrders: state => state.orders,
    getSuppliers: state => state.suppliers,
    getUsers: state => state.users,
  },
});

export default store;