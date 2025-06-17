<template>
  <AuthLayout>
    <h2 class="text-2xl font-semibold mb-6 text-center">Hi,<br>Welcome back</h2>

    <form>
      <div class="mb-4">
        <input
          type="email"
          placeholder="Email address"
          class="w-full px-4 py-2 rounded-full bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
      </div>

      <div class="mb-4">
        <input
          type="password"
          placeholder="Password"
          class="w-full px-4 py-2 rounded-full bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
      </div>

      <div class="flex justify-between items-center text-sm mb-4">
        <label class="flex items-center space-x-2">
          <input type="checkbox" class="form-checkbox">
          <span>Remember me</span>
        </label>
        <a href="/forgot-password" class="text-blue-600 hover:underline">
          Forgot password?
        </a>
      </div>

      <button type="button" class="w-full py-2 rounded-full text-white font-semibold bg-gradient-to-r from-emerald-400 to-cyan-400 hover:from-emerald-500 hover:to-cyan-500 hover:scale-110 transition-all">
        LOGIN
      </button>

      <p class="text-center text-sm mt-4">
        Don’t have account?
        <a href="/register" class="text-blue-600 hover:underline">Sign up</a>
      </p>
    </form>
  </AuthLayout>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const page = defineComponent({
  name: 'LoginPage',
});

(page as any).layout = AuthLayout

export default page

</script>
//
